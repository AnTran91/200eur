<?php
/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers\OrderHandlers;

use Doctrine\Common\Collections\ArrayCollection;

use App\Entity as Entity;

class PictureManager extends OrderHandlerBase
{
    /**
     * Validate the uploaded images
     *
     * @param string $uuid The current user directory.
     *
     * @return bool
     */
    public function validate(string $uuid): bool
    {
        // Check if images were uploaded
        if (empty($this->pictureHandler->getFilesByCurrentLocale($uuid))) {
            return true;
        }
        // Check if the each uploaded image has at least one retouch
        foreach ($this->pictureHandler->getFilesByCurrentLocale($uuid) as $file) {
            if (!is_array($file['retouch']) || count($file['retouch']) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all the user pictures
     *
     * @param Entity\Order $order
     *
     * @return ArrayCollection
     */
    public function createPicturesObjects(Entity\Order $order): ArrayCollection
    {
        $deliveryTime = $order->getDeliveryTime();
        $fields = $this->em->getRepository(Entity\Field::class)->findAllByFieldThatHavePrice();
        $pictures = new ArrayCollection();

        foreach ($this->pictureHandler->getFilesByCurrentLocale($order->getClient()->getUserDirectory(), null, $order->getUploadFolder()) as $uploadedFile) {
            $picture = $this->createNewPicture($uploadedFile, $order);

            foreach ($uploadedFile['retouch'] as $retouch) {
                $currentDeliveryTime = $this->getPictureNewPrice($retouch, $deliveryTime);

                $pictureDetail = (new Entity\PictureDetails())
                ->setPrice($currentDeliveryTime->getPrice())
                ->setRetouch($retouch)
                ->setParam(
                  (new Entity\ParamCollection())
                    ->setElements($this->pictureHandler->moveParamsFromTmpFolderToOrderFolder($uploadedFile['param'][$retouch->getId()], $order->getUploadFolder()))
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
                $pictureDetail->setPicture($picture);
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
    private function createNewPicture(array $uploadedFile, Entity\Order $order): Entity\Picture
    {
        return (new Entity\Picture())
            ->setPictureName($uploadedFile["name"])
            ->setPicturePath($uploadedFile["real_path"] ?? null)
            ->setPicturePathThumb($uploadedFile["thumbnailUrl"] ?? null)
            ->setPictureDirectory($uploadedFile["uuid"] ?? null)
            ->setOrder($order);
    }
}
