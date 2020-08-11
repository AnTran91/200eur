<?php
namespace App\Handlers\Traits;

use App\Entity as Entity;

trait OrderFormatterTrait
{
    /**
     * This function group the params by field id
     *
     * @param Entity\PictureDetails     $pictureDetail
     * @param array                     $paramPrice
     *
     * @return void
     */
    public function paramFormatter(Entity\PictureDetails $pictureDetail, array &$paramPrice): void
    {
        foreach ($pictureDetail->getFieldDetails() as $fieldDetail) {
            if (!isset($paramPrice[$fieldDetail->getField()->getId()])) {
                $paramPrice[$fieldDetail->getField()->getId()] = [
                    'field_group' => $fieldDetail->getField()->getFieldGroup(),
                    'total_price' => 0,
                    'price_per_unit' => $fieldDetail->getPrice(),
                    'picture_number' => 0
                ];
            }
            $paramPrice[$fieldDetail->getField()->getId()]['total_price'] += $fieldDetail->getPrice();
            $paramPrice[$fieldDetail->getField()->getId()]['picture_number'] ++;
        }
    }

    /**
     * This function group the PictureDetails by retouche id
     *
     * @param Entity\PictureDetails $pictureDetail
     * @param Entity\PhotoRetouchingPricing $deliveryTimePrice
     * @param array $initialFiles
     *
     * @return void
     */
    public function retouchFormatter(Entity\PictureDetails $pictureDetail, Entity\PhotoRetouchingPricing $deliveryTimePrice, array &$initialFiles):void
    {
        if (!isset($initialFiles[$pictureDetail->getRetouch()->getId()])) {
            $initialFiles[$pictureDetail->getRetouch()->getId()] = [
                'deliveryTime' => $deliveryTimePrice->getOrderDeliveryTime()->isGlobal() ? null : $deliveryTimePrice->getOrderDeliveryTime(),
                'retouch' => $pictureDetail->getRetouch(),
                'price' => 0,
                'picture_number' => 0
            ];
        }

        $initialFiles[$pictureDetail->getRetouch()->getId()]['price'] += $pictureDetail->getPrice();
        $initialFiles[$pictureDetail->getRetouch()->getId()]['picture_number'] ++;
        $initialFiles[$pictureDetail->getRetouch()->getId()]['price_per_unit'] = ($initialFiles[$pictureDetail->getRetouch()->getId()]['price'] / $initialFiles[$pictureDetail->getRetouch()->getId()]['picture_number']);
    }

    /**
     * This function group the params by field id
     *
     * @param Entity\PictureDetails     $pictureDetail
     * @param Entity\OrderDeliveryTime  $deliveryTime
     * @param array                     $retouchPrice
     * @param array                     $paramPrice
     *
     * @return void
     */
    protected function pictureDetailsFormatter(Entity\PictureDetails $pictureDetail, Entity\OrderDeliveryTime $deliveryTime, array &$retouchPrice, array &$paramPrice)
    {
        $currentDeliveryTime = $this->getPictureNewPrice($pictureDetail->getRetouch(), $deliveryTime);

        $this->paramFormatter($pictureDetail, $paramPrice);
        $this->retouchFormatter($pictureDetail, $currentDeliveryTime, $retouchPrice);
    }

    /**
     * This function group the params by field id
     *
     * @param Entity\Picture            $picture
     * @param Entity\OrderDeliveryTime  $deliveryTime
     * @param array                     $retouchPrice
     * @param array                     $paramPrice
     *
     * @return void
     */
    protected function pictureFormatter(Entity\Picture $picture, Entity\OrderDeliveryTime $deliveryTime, array &$retouchPrice, array &$paramPrice):void
    {
        foreach ($picture->getPictureDetail() as $pictureDetail) {
            $this->pictureDetailsFormatter($pictureDetail, $deliveryTime, $retouchPrice, $paramPrice);
        }
    }
}