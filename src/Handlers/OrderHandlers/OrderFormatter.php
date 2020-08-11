<?php
/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers\OrderHandlers;

use App\Entity as Entity;
use App\Handlers\Traits\OrderFormatterTrait;

class OrderFormatter extends OrderHandlerBase
{
    use OrderFormatterTrait;

    /**
     * Return The formatted order
     *
     * @param Entity\User $user     The current user
     * @param array $data           The new data
     *
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function saveAndDisplayTemporaryOrder(Entity\User $user, array $data = array()): array
    {
        $initialFiles = array();
        $paramPrice = array();
        $locale = $this->sessionManager->get('_locale', $this->defaultLocale);
        $fields = $this->em->getRepository(Entity\Field::class)->findAllByFieldThatHavePrice(null);
        $deliveryTime = $this->sessionManager->get('deliveryTime', $this->em->getRepository(Entity\OrderDeliveryTime::class)->findTheDefaultField());

        if (isset($data['deliveryTime']) && !is_null($data['deliveryTime'])) {
            $deliveryTime = $data['deliveryTime'];
        }
        // dump(sizeof($this->pictureHandler->getFilesByCurrentLocale($user->getUserDirectory(), $locale)));exit;
        foreach ($this->pictureHandler->getFilesByCurrentLocale($user->getUserDirectory(), $locale) as $uploadedFile) {
            $paramPrice = $this->getParamsTotalPrice($uploadedFile['param'] ?? [], $fields, $paramPrice);
            foreach ($uploadedFile['retouch'] as $retouch) {
                $initialFiles = $this->getRetouchTotalPrice($retouch, $deliveryTime, $initialFiles);
            }

            // get number of picture in order
            if (!isset($initialFiles[$retouch->getId()]['total_picture'])) {
                $initialFiles[$retouch->getId()]['total_picture'] = 0;
            }
            $initialFiles[$retouch->getId()]['total_picture'] += 1;
        }
        $totalPrices = $this->calculatePrices($initialFiles, array_sum(array_column($paramPrice, 'total_price')), $user);

        return $this->orderFormattedToArray($paramPrice, $totalPrices, $initialFiles);
    }

    /**
     * Return Get order total price
     *
     * @param Entity\Order $order
     *
     * @return array
     */
    public function getFormattedOrder(Entity\Order $order): array
    {
        $initialFiles = array();
        $paramPrice = array();

        foreach ($order->getPictures() as $picture) {
            foreach ($picture->getPictureDetail() as $pictureDetail) {
                $deliveryTime = $this->getPictureNewPrice($pictureDetail->getRetouch(), $order->getDeliveryTime());

                $this->paramFormatter($pictureDetail, $paramPrice);
                $this->retouchFormatter($pictureDetail, $deliveryTime, $initialFiles);
            }
        }

        $totalToPay = $order->getTotalAmount() - $order->getTotalReductionAmount();
        $tax = $totalToPay * ($order->getTaxPercentage() / 100);

        $totalPrices = [
          'dutyFree' => $order->getTotalAmount(),
          'tax' => $tax,
          'taxPercentage' => $order->getTaxPercentage(),
          'total' => $order->getTotalAmount() + $tax,
          'totalReductionPrice' => $order->getTotalReductionAmount(),
          'totalReductionPercentage' => @($totalToPay / $order->getTotalAmount()) * 100,
          'totalReductionPicture' => $order->getTotalReductionOnPictures(),
          'totalToPay' => $totalToPay + $tax
        ];

        return $this->orderFormattedToArray($paramPrice, $totalPrices, $initialFiles);
    }

    /**
     * OrderCreation to array Formatted
     *
     * @param array $paramPrice
     * @param array $prices
     * @param array $initialFiles
     * @return array
     */
    private function orderFormattedToArray(array $paramPrice, array $prices, array $initialFiles): array
    {
        return [
          'params_prices' => $paramPrice,
          'price_excluding_tax' => $prices['dutyFree'],
          'total_tva_price' => $prices['tax'],
          'total_including_tax' => $prices['total'],
          'total_reduction' => $prices['totalReductionPercentage'],
          'total_reduction_price' => $prices['totalReductionPrice'],
          'total_including_tax_after_reduction' => $prices['totalToPay'],
          'total_reduction_pictures' => $prices['totalReductionPicture'],
          'total_pictures' => array_sum(array_column($initialFiles, 'picture_number')),
          'uploads' => array_values($initialFiles)
        ];
    }

    /**
     * calculates the price for all selected retouches
     *
     * @param Entity\Retouch $retouch
     * @param Entity\OrderDeliveryTime $deliveryTime
     *
     * @param array $result
     * @return array
     */
    private function getRetouchTotalPrice(Entity\Retouch $retouch, Entity\OrderDeliveryTime $deliveryTime, array $result): array
    {
        if (!isset($result[$retouch->getId()])) {
            $currentDeliveryTime = $this->getPictureNewPrice($retouch, $deliveryTime);
            $result[$retouch->getId()] = ['retouch' => $retouch, 'deliveryTime' => $currentDeliveryTime->getOrderDeliveryTime()->isGlobal() ? null :$currentDeliveryTime->getOrderDeliveryTime(), 'price_after_reduction' => 0, 'price_per_unit' => $currentDeliveryTime->getPrice(), 'price' => 0, 'picture_number' => 0];
        }

        $result[$retouch->getId()]['price'] += $result[$retouch->getId()]['price_per_unit'];
        $result[$retouch->getId()]['picture_number'] ++;

        return $result;
    }
}
