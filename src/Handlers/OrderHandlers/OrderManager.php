<?php
/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers\OrderHandlers;

use App\Handlers\Traits\OrderFormatterTrait;
use Doctrine\Common\Collections\ArrayCollection;

use App\Entity as Entity;

class OrderManager extends OrderHandlerBase
{
	use OrderFormatterTrait;
	
	/**
	 * Recalculate the order price
	 *
	 * @param Entity\Order $order The order object.
	 * @param array|null $data
	 * @return Entity\Order
	 * @throws \Doctrine\DBAL\ConnectionException
	 */
	public function updateOrderPrice(Entity\Order $order, ?array $data = null): Entity\Order
	{
		try {
			$fields = $this->em->getRepository(Entity\Field::class)->findAllByFieldThatHavePrice(null);
			
			$retouchPrices = array();
			$paramPrice = array();
			
			$deliveryTime = $order->getDeliveryTime();
			if (isset($data['deliveryTime']) && !is_null($data['deliveryTime'])) {
				$deliveryTime = $data['deliveryTime'];
			}
			
			foreach ($order->getPictures() as $picture) {
				$this->updatePicturePrice($picture, $deliveryTime);
				$this->updateFieldDetails($picture, $fields);
				$this->pictureFormatter($picture, $deliveryTime, $retouchPrices, $paramPrice);
			}
			
			/**
			 * @var int $dutyFree
			 * @var int $taxPercentage
			 * @var int $totalReductionPercentage
			 * @var int $totalReductionPicture
			 * @var int $totalReductionPrice
			 */
			extract($this->calculatePrices($retouchPrices, array_sum(array_column($paramPrice, 'total_price')), $order->getClient(), $order->getPromotion()));
			
			$order->setTotalAmount($dutyFree)
				->setTaxPercentage($taxPercentage)
				->setReductionPercentage($totalReductionPercentage)
				->setTotalReductionOnPictures($totalReductionPicture)
				->setTotalReductionAmount($totalReductionPrice);
			
			if (!is_null($order->getPromotion()) && $order->getPromotion() instanceof Entity\PictureDiscount && ($order->getTotalReductionAmount() === 0 || $order->getTotalReductionOnPictures() === 0)) {
				$order->setPromotion(null);
			}
			
			$this->em->flush();
		} catch (\Throwable $e) {
			// Rollback the failed transaction attempt
			$this->em->getConnection()->rollback();
		} finally {
			return $order;
		}
	}
	
	/**
	 * Update the order status
	 *
	 * @param Entity\Order $order The order object.
	 *
	 * @return void
	 * @throws \Doctrine\DBAL\ConnectionException
	 */
	public function updateOrderStatus(Entity\Order $order): void
	{
		try {
			$status = Entity\Order::COMPLETED;
			foreach ($order->getPictures() as $picture) {
				foreach ($picture->getPictureDetail() as $pictureDetail) {
					// if picture status is valid
					if (!is_null($pictureDetail->getReturnedPicture()) && ($pictureDetail->getReturnedPicture()->getStatus() == Entity\Picture::REFUSED || $pictureDetail->getReturnedPicture()->getStatus() !== Entity\Picture::VALIDATED)) {
						$status = Entity\Order::PARTIALLY_COMPLETED;
					}
					if (is_null($pictureDetail->getReturnedPicture()) || $pictureDetail->getReturnedPicture()->getStatus() !== Entity\Picture::VALIDATED) {
						continue;
					}
				}
			}
			
			$order->setOrderStatus($status);
			$this->em->flush();
		} catch (\Throwable $e) {
			// Rollback the failed transaction attempt
			$this->em->getConnection()->rollback();
		}
	}
	
	/**
	 * Create params and update the database
	 *
	 * @param array $retouchObjects
	 * @param array $validParams
	 * @param Entity\Picture $picture
	 *
	 * @return void
	 * @throws \Doctrine\DBAL\ConnectionException
	 */
	public function editAndUpdateOrder(array $retouchObjects, array $validParams, Entity\Picture $picture = null)
	{
		try {
			$retouchPrices = array();
			$paramPrice = array();
			
			$fields = $this->em->getRepository(Entity\Field::class)->findAllByFieldThatHavePrice(null);
			// suspend auto-commit
			$this->em->getConnection()->beginTransaction();
			// save params
			foreach ($retouchObjects as $retouch) {
				$pictureDetail = $picture->getPictureDetail()->filter(function (Entity\PictureDetails $pictureDetail) use ($retouch) {
					return $retouch->getId() == $pictureDetail->getRetouch()->getId();
				});
				
				if ($pictureDetail->isEmpty()) {
					$currentDeliveryTime = $this->getPictureNewPrice($retouch, $picture->getOrder()->getDeliveryTime());
					
					$pictureDetail = $this->createPictureDetails($retouch, $currentDeliveryTime, $validParams[$retouch->getId()])
						->setPicture($picture);
					$picture->addPictureDetail($pictureDetail);
					
					$this->em->persist($pictureDetail);
				} else {
					$pictureDetail->first()->getParam()->setElements($validParams[$retouch->getId()]);
				}
			}
			
			$this->updateFieldDetails($picture, $fields);
			$this->pictureFormatter($picture, $picture->getOrder()->getDeliveryTime(), $retouchPrices, $paramPrice);
			foreach ($picture->getOrder()->getPictures() as $currentPicture) {
				if ($currentPicture->getId() == $picture->getId()) {
					continue;
				}
				$this->updateFieldDetails($picture, $fields);
				$this->pictureFormatter($currentPicture, $picture->getOrder()->getDeliveryTime(), $retouchPrices, $paramPrice);
			}
			
			/**
			 * @var int $dutyFree
			 * @var int $taxPercentage
			 * @var int $totalReductionPercentage
			 * @var int $totalReductionPicture
			 * @var int $totalReductionPrice
			 */
			extract($this->calculatePrices($retouchPrices, array_sum(array_column($paramPrice, 'total_price')), $picture->getOrder()->getClient(), $picture->getOrder()->getPromotion()));
			
			$picture->getOrder()->setTotalAmount($dutyFree)
				->setTaxPercentage($taxPercentage)
				->setReductionPercentage($totalReductionPercentage)
				->setTotalReductionOnPictures($totalReductionPicture)
				->setTotalReductionAmount($totalReductionPrice);
			
			$this->em->flush();
			$this->em->getConnection()->commit();
		} catch (\Doctrine\DBAL\ConnectionException $e) {
			$this->em->getConnection()->rollback();
		}
	}
	
	/**
	 * Get The last order number
	 *
	 * @return int
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getTheLastOrderNumber(): int
	{
		return (int)++$this->em
			->getRepository(Entity\Order::class)
			->findLastOrderNumber()['lastNumber'];
	}
	
	/**
	 * Get The last order number
	 *
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getTheLastInvoiceNumber(): int
	{
		return (int)++$this->em
			->getRepository(Entity\Invoice::class)
			->findLastInvoiceNumber()['lastNumber'];
	}
	
	/**
	 * If picture do have one or multiple prices
	 *
	 * @param Entity\User $user
	 *
	 * @return bool
	 */
	public function doPicturesHasMultiplePrices(Entity\User $user): bool
	{
		foreach ($this->pictureHandler->getFilesByCurrentLocale($user->getUserDirectory()) as $uploadedFile) {
			foreach ($uploadedFile['retouch'] as $retouch) {
				if ($retouch->getPricings()->count() !== 1) {
					return false;
				}
			}
		}
		return true;
	}
	
	/**
	 * Update field details params
	 *
	 * @param Entity\Picture $picture
	 * @param array $fields
	 */
	private function updateFieldDetails(Entity\Picture $picture, array $fields): void
	{
		foreach ($picture->getPictureDetail() as $pictureDetail) {
			$fieldDetails = new ArrayCollection();
			$_fieldDetailsArray = new ArrayCollection();
			if ($pictureDetail->getFieldDetails()->count() == 0) {
				$_field = $this->getParamsTotalPrice([$pictureDetail->getParam()->getElements()], $fields, []);

				foreach ($_field as $field_id => $data) {
					$field_object = $this->em->getRepository(Entity\Field::class)->find($field_id);

					$_fieldDetails = new Entity\FieldDetails();
					$_fieldDetails->setPrice($data['price_per_unit']);
					$_fieldDetails->setPictureDetail($pictureDetail);
					$_fieldDetails->setField($field_object);

					$_fieldDetailsArray->add($_fieldDetails);
				}
			}
			else {
				if ($pictureDetail->getFieldDetails() instanceof \Doctrine\Common\Collections\ArrayCollection) {
					$_fieldDetailsArray = $pictureDetail->getFieldDetails();
				}
				else {
					foreach($pictureDetail->getFieldDetails() as $fieldDetailExit) {
						$_fieldDetailsArray->add($fieldDetailExit);
					}
				}
			}

			foreach ($this->getParamsTotalPrice([$pictureDetail->getParam()->getElements()], $fields, []) as $fieldDetail) {
				$currentFieldDetail = $this->createFieldDetails($_fieldDetailsArray, $fieldDetail);
				
				$currentFieldDetail->setPictureDetail($pictureDetail);
				$pictureDetail->addFieldDetail($currentFieldDetail);
				$fieldDetails->add($currentFieldDetail);
			}

			foreach ($pictureDetail->getFieldDetails() as $fieldDetail) {
				if ($fieldDetails->filter(function (Entity\FieldDetails $fieldDetails) use ($fieldDetail) {
					return $fieldDetails->getField()->getId() === $fieldDetail->getField()->getId();
				})->isEmpty()) {
					$pictureDetail->removeFieldDetail($fieldDetail);
				}
			}
		}
	}
	
	/**
	 * Update picture order delivery
	 *
	 * @param Entity\Picture &$picture
	 * @param Entity\OrderDeliveryTime $deliveryTime
	 *
	 * @return void
	 */
	private function updatePicturePrice(Entity\Picture &$picture, Entity\OrderDeliveryTime $deliveryTime): void
	{
		foreach ($picture->getPictureDetail() as $pictureDetail) {
			$pictureDetail->setPrice($this->getPictureNewPrice($pictureDetail->getRetouch(), $deliveryTime)->getPrice());
		}
	}
	
	/**
	 * This function create picture details
	 *
	 * @param Entity\Retouch $retouch
	 * @param Entity\PhotoRetouchingPricing $pricing
	 * @param array $validParams
	 * @return Entity\PictureDetails
	 */
	private function createPictureDetails(Entity\Retouch $retouch, Entity\PhotoRetouchingPricing $pricing, array $validParams): Entity\PictureDetails
	{
		return (new Entity\PictureDetails())
			->setRetouch($retouch)
			->setPrice($pricing->getPrice())
			->setParam(
				(new Entity\ParamCollection())
					->setElements($validParams)
			);
	}
	
	/**
	 * This function create field details
	 *
	 * @param ArrayCollection|\App\Entity\FieldDetails[] $fieldDetails
	 * @param array $fieldDetail
	 *
	 * @return Entity\FieldDetails
	 */
	private function createFieldDetails(ArrayCollection $fieldDetails, array $fieldDetail): Entity\FieldDetails
	{
		$currentFieldDetail = $fieldDetails->filter(function (Entity\FieldDetails $fieldDetails) use ($fieldDetail) {
			return $fieldDetails->getField()->getId() === $fieldDetail['field']->getId();
		});
		
		if ($currentFieldDetail->isEmpty()) {
			$currentFieldDetail = new Entity\FieldDetails();
		} else {
			$currentFieldDetail = $currentFieldDetail->first();
		}
		
		$currentFieldDetail->setPrice($fieldDetail['price_per_unit'])
			->setField($fieldDetail['field']);
		
		return $currentFieldDetail;
	}
}
