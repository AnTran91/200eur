<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers\OrderHandlers;

use App\Handlers\PictureHandler;
use App\Handlers\PromoHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Entity as Entity;

class OrderHandlerBase
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var SessionInterface
     */
    protected $sessionManager;

    /**
     * @var PictureHandler
     */
    protected $pictureHandler;

    /**
     * @var PromoHandler
     */
    protected $promoHandler;

    /**
     * @var array Tax country list
     */
    protected $countryTax;

    /**
     * @var string local
     */
    protected $defaultLocale;

    /**
     * Constructor
     *
     * @param PromoHandler              $promoHandler
     * @param PictureHandler            $pictureHandler
     * @param SessionInterface          $session
     * @param EntityManagerInterface    $em
     * @param array                     $countryTaxList
     * @param string                    $defaultLocale
     */
    public function __construct(PromoHandler $promoHandler, PictureHandler $pictureHandler, SessionInterface $session, EntityManagerInterface $em, array $countryTaxList, string $defaultLocale)
    {
        $this->em = $em;
        $this->pictureHandler = $pictureHandler;
        $this->promoHandler = $promoHandler;
        $this->countryTax = $countryTaxList;
        $this->defaultLocale = $defaultLocale;
        $this->sessionManager = $session;
    }

    /**
     * Calculates the price for all selected params
     *
     * @param array|null    $uploadedFiles
     * @param array         $fields
     * @param array         $paramPrice
     *
     * @return array
     */
    protected function getParamsTotalPrice(?array $uploadedFiles, array $fields, array $paramPrice): array
    {
        if (!is_null($uploadedFiles) && is_array($uploadedFiles)) {
            foreach ($uploadedFiles as $uploadedFile) {
                foreach ($fields as $field) {
                    if (!is_null($field->getPrice()) && isset($uploadedFile[$field->getName()]) && $uploadedFile[$field->getName()] == $field->getAddThePriceWhenValueEqualsTo()) {
                        if (!isset($paramPrice[$field->getId()])) {
                            $paramPrice[$field->getId()] = ['field_group' => $field->getFieldGroup(), 'field' => $field, 'total_price' => 0, 'price_per_unit' => $field->getPrice(), 'picture_number' => 0];
                        }
                        $paramPrice[$field->getId()]['total_price'] += $field->getPrice();
                        $paramPrice[$field->getId()]['picture_number'] ++;
                    }
                }
            }
        }
        return $paramPrice;
    }

    /**
     * Calculates the prices that will be displayed to the user
     *
     * @param array         $orderedPictures
     * @param float         $paramPrice
     * @param Entity\User   $user
     * @param Entity\Promo  $promo
     *
     * @return array
     */
    protected function calculatePrices(array $orderedPictures, float $paramPrice, Entity\User $user, ?Entity\Promo $promo = null)
    {
        $dutyFree = array_sum(array_column($orderedPictures, 'price')) + $paramPrice;

        // this function will create this tow vars ($totalReductionPrice , $totalReductionPicture)
        extract($this->promoHandler->calculatePromoReduction($orderedPictures, $user, $promo));
	
        $totalToPay = $dutyFree - $totalReductionPrice;
        $tax = $totalToPay * ($this->calculateTax($user) / 100);
        $totalReductionPercentage = @($totalToPay / $dutyFree) * 100;

        return [
            'dutyFree' => $dutyFree,
            'tax' => $tax,
            'taxPercentage' => $this->calculateTax($user),
            'total' => $dutyFree + $tax,
            'totalReductionPrice' => $totalReductionPrice,
            'totalReductionPercentage' => $totalReductionPercentage,
            'totalReductionPicture' => $totalReductionPicture,
            'totalToPay' => $totalToPay + $tax
        ];
    }

    /**
     * Get the picture new current price
     *
     * @param Entity\Retouch           $retouch
     * @param Entity\OrderDeliveryTime $deliveryTime
     *
     * @return Entity\PhotoRetouchingPricing
     */
    protected function getPictureNewPrice(Entity\Retouch $retouch, ?Entity\OrderDeliveryTime $deliveryTime): Entity\PhotoRetouchingPricing
    {
        $currentDeliveryTime = $retouch->getPricings()->filter(function (Entity\PhotoRetouchingPricing $photoRetouchingPricing) use ($deliveryTime) {
            return $photoRetouchingPricing->getOrderDeliveryTime()->getId() == $deliveryTime->getId();
        })->first();

        $currentDeliveryTime = $currentDeliveryTime ? $currentDeliveryTime : $retouch->getPricings()->first();

        return $currentDeliveryTime;
    }

    /**
     * Get Tax By country
     *
     * @param Entity\User $user
     *
     * @return float
     */
    public function calculateTax(Entity\User $user): float
    {
        foreach ($this->countryTax['options'] as $tax) {
            if ($user->getBillingAddress()->getCountry() == $tax['country']) {
                return $tax['rate'];
            }
        }
        return $this->countryTax['default_rate'];
    }
}
