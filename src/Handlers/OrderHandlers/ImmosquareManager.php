<?php

namespace App\Handlers\OrderHandlers;

use App\Entity as Entity;
use App\Utils\Traits\FileGuesserTrait;
use Doctrine\Common\Collections\ArrayCollection;

class ImmosquareManager extends OrderHandlerBase
{
    use FileGuesserTrait;

    /**
     * @param Entity\Order $order
     * @return array
     */
    public function formatCreatedOrder(Entity\Order $order): array
    {
        $formattedOrder = [
            "identify" => $order->getId(),
            "delivery_time" => $order->getDeliveryTime()->getOrderDeliveryCode(),
            "order_number" => sprintf("%06d", $order->getorderNumber()),
            "order_creation_date" => $order->getCreationDate(),
            "images" => [],
            "tax_percentage" => sprintf("%d%%", $order->getTaxPercentage()),
            "total_amount" => sprintf("%d€", number_format($order->getAmountIncludingTaxAfterReduction(), 2))
        ];

        foreach ($order->getPictures() as $picture){
            foreach ($picture->getPictureDetail() as $pictureDetails){
                $formattedOrder["images"][]= [
                    "image_name" => $picture->getPictureName(),
                    "image_path" => $picture->getPicturePath(),
                    "services" => [
                        "service" => $pictureDetails->getRetouch()->getRetouchCode(),
                        "service_price" => sprintf("%d€", number_format($pictureDetails->getPrice(), 2)),

                        "settings" => $pictureDetails->getParam()->getElements(),
                        "settings_price" => sprintf("%d€", number_format(array_sum($pictureDetails->getFieldDetails()->map(function (Entity\FieldDetails $fieldDetail){
                            return $fieldDetail->getPrice();
                        })->toArray()), 2))
                    ]
                ];
            }
        }

        return $formattedOrder;
    }

    /**
     * @param Entity\Order $order
     * @return array
     */
    public function formatReadyOrder(Entity\Order $order): array
    {
        $formattedOrder = [
            "identify" => $order->getId(),
            "delivery_time" => $order->getDeliveryTime()->getOrderDeliveryCode(),
            "order_number" => $order->getorderNumber(),
            "order_creation_date" => $order->getCreationDate(),
            "order_delivery_date" => $order->getDeliveranceDate(),
            "images" => [],
            "total_amount" => sprintf("%d€", number_format($order->getAmountIncludingTaxAfterReduction(), 2))
        ];

        foreach ($order->getPictures() as $picture){
            $services = [];
            foreach ($picture->getPictureDetail() as $pictureDetails){
                 $services[] = [
                    "service" => $pictureDetails->getRetouch()->getRetouchCode(),
                    "retouched_image_URL" => str_replace(' ', '%20', $pictureDetails->getReturnedPicture()->getPicturePath())
                 ];
            }

            $formattedOrder["images"][] = [
                "origin_image_URL" => $picture->getPicturePath(),
                "retouched_image_URLS" => $services
            ];
        }

        return $formattedOrder;
    }


    /**
     * Get all the user pictures
     *
     * @param Entity\Order $order
     * @param array $uploadedFiles
     *
     * @return ArrayCollection
     */
    public function createPicturesObjectsFromArray(Entity\Order $order, array $uploadedFiles): ArrayCollection
    {
        $deliveryTime = $order->getDeliveryTime();
        $fields = $this->em->getRepository(Entity\Field::class)->findAllByFieldThatHavePrice();
        $pictures = new ArrayCollection();

        foreach ($uploadedFiles as $uploadedFile) {
            $picture = $this->createNewImmosquarePicture($uploadedFile, $order);

            foreach ($uploadedFile['services'] as $service) {
                $currentDeliveryTime = $this->getPictureNewPrice($service['service'], $deliveryTime);

                $pictureDetail = (new Entity\PictureDetails())
                    ->setPrice($currentDeliveryTime->getPrice())
                    ->setRetouch($service['service'])
                    ->setParam(
                        (new Entity\ParamCollection())
                            ->setElements($this->pictureHandler->moveParamsFromTmpFolderToOrderFolder($service['settings'], $order->getUploadFolder()))
                    );
                foreach ($this->getParamsTotalPrice([$pictureDetail->getParam()->getElements()], $fields, []) as $fieldDetail) {
                    $pictureDetail->addFieldDetail(
                        (new Entity\FieldDetails())
                            ->setPrice($fieldDetail['price_per_unit'])
                            ->setField($fieldDetail['field'])
                            ->setPictureDetail($pictureDetail)
                    );
                }
                $picture->addPictureDetail($pictureDetail);
            }

            $pictures->add($picture);
        }
        return $pictures;
    }

    /**
     * Create new picture object.
     *
     * @param array $uploadedFile
     * @param Entity\Order $order
     *
     * @return Entity\Picture
     */
    private function createNewImmosquarePicture(array $uploadedFile, Entity\Order $order): Entity\Picture
    {
        return (new Entity\Picture())
            ->setPictureName($this->doGuessFileName($uploadedFile['url']))
            ->setPicturePath($uploadedFile["url"] ?? null)
            ->setPicturePathThumb($uploadedFile["url"] ?? null)
            ->setPictureDirectory($uploadedFile["uuid"] ?? null)
            ->setOrder($order);
    }
}