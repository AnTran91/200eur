<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use App\Events\ChunkedFileUploadEvent;
use App\Utils\Events;

use App\Handlers\FileHandler;
use Doctrine\ORM\EntityManagerInterface;

use App\Exception\FileSystemException;

use App\Entity\Picture;

/**
 * @see \App\Events\ChunkedFileUploadEvent class.
 */
final class ChunkedFileUploadSubscriber implements EventSubscriberInterface
{
    /**
     * @var FileHandler
     */
    private $fileHandler;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Constructor
     *
     * @param FileHandler             $uploader
     * @param EntityManagerInterface  $em
     */
    public function __construct(FileHandler $uploader, EntityManagerInterface $em)
    {
        $this->fileHandler = $uploader;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
          Events::ON_UPLOAD_FINISHED_PICTURE => [
            array('onUpload', 0),
            array('onPostGenerateThumb', -10),
            array('onPostGenerateWatermarked', -15),
            array('onPostUpdatePicture', -20),
            array('cleanupChunks', -30)
          ],
          Events::ON_UPLOAD_GIF_PICTURE => [
            array('onUpload', 0),
            array('onPostGenerateThumb', -10),
            array('onPostGenerateGifWatermarked', -15),
            array('onPostUpdateGifPicture', -20),
            array('cleanupChunks', -30)
          ],
          Events::ON_UPLOAD_MP4_FILE => [
            array('onUpload', 0),
            array('onPostGenerateThumb', -10),
            array('onPostGenerateMP4Watermarked', -15),
            array('onPostUpdateMP4Picture', -20),
            array('cleanupChunks', -30)
          ],
          Events::ON_UPLOAD_PAINTED_PICTURE => [
            array('onUpload', 0),
            array('onPostGenerateThumb', -10),
            array('onDeletePaintedPicture', -40),
            array('onPostUpdatePaintedPicture', -50),
            array('cleanupChunks', -30),
          ]
        ];
    }

    /**
     * do handle Upload
     *
     * @param ChunkedFileUploadEvent $uploadEvent
     */
    public function onUpload(ChunkedFileUploadEvent $uploadEvent)
    {
        try {
            if (!$uploadEvent->getForm()->isSubmitted() || !$uploadEvent->getForm()->isValid()) {
                $uploadEvent->setData(["error" => \App\Utils\Tools::getErrorMessages($uploadEvent->getForm())]);
                $uploadEvent->setSuccess(false);
                $uploadEvent->stopPropagation();
            }

            $picture = $uploadEvent->getPicture();
            $pictureDir = $picture->getPictureDirectory();
            $userDir = $uploadEvent->getClient()->getUserDirectory();
            $orderDir = $uploadEvent->getOrder()->getUploadFolder();

            $result =  $this->fileHandler->handleOrderPictureUpload($uploadEvent->getForm()->getData(), $userDir, $orderDir, $pictureDir);

            $uploadEvent->setData($result);
            $uploadEvent->setSuccess(true);

            if (!isset($result['uploaded']) || !$result['uploaded']) {
                $uploadEvent->stopPropagation();
            }
        } catch (FileSystemException $e) {
            $uploadEvent->setData(["error" => $e->getMessage()]);
            $uploadEvent->setSuccess(false);
            $uploadEvent->stopPropagation();
        }
    }

    /**
     * Generate thumbnail after the upload complete
     *
     * @param ChunkedFileUploadEvent $uploadEvent
     */
    public function onPostGenerateThumb(ChunkedFileUploadEvent $uploadEvent)
    {
        $data = $uploadEvent->getData();

        try {
            $thumbPath = $this->fileHandler->doGenerateThumbnail($data['fileFullPath'], $data['fileDir'], $data['name']);
            $data['thumbnailUrl'] = $thumbPath;
            $uploadEvent->setData($data);
        } catch (FileSystemException $e) {
            $uploadEvent->setData(["error" => $e->getMessage()]);
            $uploadEvent->setSuccess(false);
            $uploadEvent->stopPropagation();
        }
    }

    /**
     * Generate thumbnail after the upload complete
     *
     * @param ChunkedFileUploadEvent $uploadEvent
     */
    public function onPostGenerateWatermarked(ChunkedFileUploadEvent $uploadEvent)
    {
        $data = $uploadEvent->getData();

        try {
            $watermarkedPath = $this->fileHandler->doGenerateWatermarked($data['fileFullPath'], $data['fileDir'], $data['name']);
            $data['watermarkedUrl'] = $watermarkedPath;
            $uploadEvent->setData($data);
        } catch (FileSystemException $e) {
            $uploadEvent->setData(["error" => $e->getMessage()]);
            $uploadEvent->setSuccess(false);
            $uploadEvent->stopPropagation();
        }
    }

    /**
     * Generate thumbnail after the upload complete
     *
     * @param ChunkedFileUploadEvent $uploadEvent
     */
    public function onPostGenerateGifWatermarked(ChunkedFileUploadEvent $uploadEvent)
    {
        $data = $uploadEvent->getData();

        try {
            $watermarkedPath = $this->fileHandler->doGenerateGifWatermarked($data['fileFullPath'], $data['fileDir'], $data['name']);
            $data['watermarkedUrl'] = $watermarkedPath;
            $uploadEvent->setData($data);
        } catch (FileSystemException $e) {
            $uploadEvent->setData(["error" => $e->getMessage()]);
            $uploadEvent->setSuccess(false);
            $uploadEvent->stopPropagation();
        }
    }

    /**
     * Generate thumbnail after the upload complete
     *
     * @param ChunkedFileUploadEvent $uploadEvent
     */
    public function onPostGenerateMP4Watermarked(ChunkedFileUploadEvent $uploadEvent)
    {
        $data = $uploadEvent->getData();

        try {
            $watermarkedPath = $this->fileHandler->doGenerateMP4Watermarked($data['fileFullPath'], $data['fileDir'], $data['name']);
            $data['watermarkedUrl'] = $watermarkedPath;
            $uploadEvent->setData($data);
        } catch (FileSystemException $e) {
            $uploadEvent->setData(["error" => $e->getMessage()]);
            $uploadEvent->setSuccess(false);
            $uploadEvent->stopPropagation();
        }
    }

    /**
     * Update the database after the upload complete
     *
     * @param ChunkedFileUploadEvent $uploadEvent
     */
    public function onPostUpdatePicture(ChunkedFileUploadEvent $uploadEvent)
    {
        $data = $uploadEvent->getData();

        try {
            $pictureDetail = $uploadEvent->getPictureDetail();
            $picture = $pictureDetail->getPicture();
            $fileName = sprintf('%04d (%s) %s', $picture->getId(), $pictureDetail->getRetouch()->getTitle(), $picture->getPictureName());

            if (is_null($pictureDetail->getReturnedPicture())) {
                $picture = (new Picture())
                  ->setPictureName($fileName)
                  ->setPicturePath($data["filePath"])
                  ->setPictureDirectory($data["fileUid"])
                  ->setStatus(Picture::AWAITING_FOR_VERIFICATION)
                  ->setPicturePathThumb($data["thumbnailUrl"])
                  ->setWatermarkedPicturePath($data["watermarkedUrl"]);

                $pictureDetail->setReturnedPicture($picture);

                $this->em->persist($picture);
                $this->em->persist($pictureDetail);
            } else {
                $pictureDetail->getReturnedPicture()
                  ->setPictureName($fileName)
                  ->setPicturePath($data["filePath"])
                  ->setPictureDirectory($data["fileUid"])
                  ->setStatus(Picture::AWAITING_FOR_VERIFICATION)
                  ->setPicturePathThumb($data["thumbnailUrl"])
                  ->setWatermarkedPicturePath($data["watermarkedUrl"]);
            }

            $data['fileName'] = $fileName;
            $uploadEvent->setData($data);

            $this->em->flush();
        } catch (\Exception $e) {
            $uploadEvent->setData(["error" => $e->getMessage()]);
            $uploadEvent->setSuccess(false);
            $uploadEvent->stopPropagation();
        }
    }

    /**
     * Update the database after the upload gif complete
     *
     * @param ChunkedFileUploadEvent $uploadEvent
     */
    public function onPostUpdateGifPicture(ChunkedFileUploadEvent $uploadEvent)
    {
        $data = $uploadEvent->getData();

        try {
            $pictureDetail = $uploadEvent->getPictureDetail();
            $picture = $pictureDetail->getPicture();

            $image = new \SplFileInfo($data["filePath"]);
            $fileName = $image->getFilename();
            $pictureReturned = $pictureDetail->getReturnedGifPicture();
 
            if (is_null($pictureDetail->getReturnedGifPicture())) {
                $picture = (new Picture())
                    ->setPictureName($fileName)
                    ->setPicturePath($data["filePath"])
                    ->setPictureDirectory($data["fileUid"])
                    ->setStatus(Picture::AWAITING_FOR_VERIFICATION)
                    ->setPicturePathThumb($data["thumbnailUrl"])
                    ->setWatermarkedPicturePath($data["watermarkedUrl"]);
                
                $pictureDetail->setReturnedGifPicture($picture);
                
                $this->em->persist($picture);
                $this->em->persist($pictureDetail);
            } else {
                $pictureDetail->getReturnedGifPicture()
                    ->setPictureName($fileName)
                    ->setPicturePath($data["filePath"])
                    ->setPictureDirectory($data["fileUid"])
                    ->setStatus(Picture::AWAITING_FOR_VERIFICATION)
                    ->setPicturePathThumb($data["thumbnailUrl"])
                    ->setWatermarkedPicturePath($data["watermarkedUrl"]);
            }

            $data['fileName'] = $fileName;
            $uploadEvent->setData($data);

            $this->em->flush();
        } catch (\Exception $e) {
            $uploadEvent->setData(["error" => $e->getMessage()]);
            $uploadEvent->setSuccess(false);
            $uploadEvent->stopPropagation();
        }
    }

    /**
     * Update the database after the upload MP4 complete
     *
     * @param ChunkedFileUploadEvent $uploadEvent
     */
    public function onPostUpdateMP4Picture(ChunkedFileUploadEvent $uploadEvent)
    {
        $data = $uploadEvent->getData();

        try {
            $pictureDetail = $uploadEvent->getPictureDetail();
            $picture = $pictureDetail->getPicture();

            $video = new \SplFileInfo($data["filePath"]);
            $fileName = $video->getFilename();
            $pictureReturned = $pictureDetail->getReturnedMP4Picture();

            if (is_null($pictureDetail->getReturnedMP4Picture())) {
                $picture = (new Picture())
                    ->setPictureName($fileName)
                    ->setPicturePath($data["filePath"])
                    ->setPictureDirectory($data["fileUid"])
                    ->setStatus(Picture::AWAITING_FOR_VERIFICATION)
                    ->setPicturePathThumb($data["thumbnailUrl"])
                    ->setWatermarkedPicturePath($data["watermarkedUrl"]);
                
                $pictureDetail->setReturnedMP4Picture($picture);
                
                $this->em->persist($picture);
                $this->em->persist($pictureDetail);
            } else {
                $pictureDetail->getReturnedMP4Picture()
                    ->setPictureName($fileName)
                    ->setPicturePath($data["filePath"])
                    ->setPictureDirectory($data["fileUid"])
                    ->setStatus(Picture::AWAITING_FOR_VERIFICATION)
                    ->setPicturePathThumb($data["thumbnailUrl"])
                    ->setWatermarkedPicturePath($data["watermarkedUrl"]);
            }

            $data['fileName'] = $fileName;
            $uploadEvent->setData($data);

            $this->em->flush();
        } catch (\Exception $e) {
            $uploadEvent->setData(["error" => $e->getMessage()]);
            $uploadEvent->setSuccess(false);
            $uploadEvent->stopPropagation();
        }
    }

    /**
     * Update the database after the upload complete
     *
     * @param ChunkedFileUploadEvent $uploadEvent
     */
    public function onPostUpdatePaintedPicture(ChunkedFileUploadEvent $uploadEvent)
    {
        $data = $uploadEvent->getData();

        try {
            $pictureDetail = $uploadEvent->getPictureDetail();
            
            if (is_null($pictureDetail->getReturnedPicture())) {
                $picture = (new Picture())
                  ->setPaintedPicturePath($data["filePath"])
                  ->setPaintedPicturePathThumb($data["thumbnailUrl"])
                  ;
                $pictureDetail->setReturnedPicture($picture);

                $this->em->persist($picture);
                $this->em->persist($pictureDetail);
            } else {
                $pictureDetail->getReturnedPicture()
                              ->setPaintedPicturePath($data["filePath"])
                              ->setPaintedPicturePathThumb($data["thumbnailUrl"]);
            }

            $uploadEvent->setData($data);

            $this->em->flush();
        } catch (\Exception $e) {
            $uploadEvent->setData(["error" => $e->getMessage()]);
            $uploadEvent->setSuccess(false);
            $uploadEvent->stopPropagation();
        }
    }

    /**
     * Clean up the chunk dir
     *
     * @param ChunkedFileUploadEvent $uploadEvent
     */
    public function cleanupChunks(ChunkedFileUploadEvent $uploadEvent)
    {
        $data = $uploadEvent->getData();
        try {
            if (array_key_exists('chunksFolder', $data)) {
                $this->fileHandler->cleanupDir($data['chunksFolder']);
            }
        } catch (FileSystemException $e) {
            $uploadEvent->setData(["error" => $e->getMessage()]);
            $uploadEvent->setSuccess(false);
            $uploadEvent->stopPropagation();
        }
    }

    /**
     * do delete the old picture
     *
     * @param ChunkedFileUploadEvent $uploadEvent
     */
    public function onDeletePaintedPicture(ChunkedFileUploadEvent $uploadEvent)
    {
        try {
            $picture = $uploadEvent->getReturnedPicture();
            if (is_null($picture) || is_null($picture->getPaintedPicturePath())) {
                return;
            }
            $this->fileHandler->removeFileOrDirectory($picture->getPaintedPicturePath());
        } catch (FileSystemException $e) {
            $uploadEvent->setData(["error" => $e->getMessage()]);
            $uploadEvent->setSuccess(false);
            $uploadEvent->stopPropagation();
        }
    }
    //
    // /**
    //  * do handle Upload
    //  *
    //  * @param ChunkedFileUploadEvent $uploadEvent
    //  */
    // public function onUploadPaintedPicture(ChunkedFileUploadEvent $uploadEvent)
    // {
    //     $request = $uploadEvent->getRequest();
    //     try {
    //         if (!$uploadEvent->getForm()->isSubmitted() || !$uploadEvent->getForm()->isValid()) {
    //             throw new FileSystemException(\App\Utils\Tools::getErrorMessages($uploadEvent->getForm()));
    //         }
    //
    //         $picture = $uploadEvent->getPicture();
    //         $pictureDir = $picture->getPictureDirectory();
    //         $userDir = $uploadEvent->getClient()->getUserDirectory();
    //         $orderDir = $uploadEvent->getOrder()->getUploadFolder();
    //
    //         $result =  $this->fileHandler->handleChunkedImgUpload($uploadEvent->getForm()->getData(), $userDir, $orderDir, $pictureDir, false);
    //
    //         if (!isset($result['uploaded']) || !$result['uploaded']) {
    //             $uploadEvent->stopPropagation();
    //         }
    //         $uploadEvent->setData($result);
    //         $uploadEvent->setSuccess(true);
    //     } catch (FileSystemException $e) {
    //         $uploadEvent->setData(["error" => $e->getMessage()]);
    //         $uploadEvent->setSuccess(false);
    //         $uploadEvent->stopPropagation();
    //     }
    // }
    //
    //
    // /**
    //  * Update the database after the upload complete
    //  *
    //  * @param ChunkedFileUploadEvent $uploadEvent
    //  */
    // public function onPostUpdatePaintedPicture(ChunkedFileUploadEvent $uploadEvent)
    // {
    //     $data = $uploadEvent->getData();
    //
    //     try {
    //         $picture = $uploadEvent->getPicture();
    //
    //         $picture->setPaintedPicturePath($data["filePath"]);
    //         $picture->setPaintedPicturePathThumb($data["thumbnailUrl"]);
    //
    //         $this->em->flush();
    //     } catch (DBException $e) {
    //         $uploadEvent->setData(["error" => $e->getMessage()]);
    //         $uploadEvent->setSuccess(false);
    //         $uploadEvent->stopPropagation();
    //     }
    // }
    //
    // /**
    //  * Generate thumbnail after the upload complete
    //  *
    //  * @param ChunkedFileUploadEvent $uploadEvent
    //  */
    // public function onPostGenerateThumbForPaintedPicture(ChunkedFileUploadEvent $uploadEvent)
    // {
    //     $data = $uploadEvent->getData();
    //
    //     try {
    //         $thumbPath = $this->fileHandler->doGenerateThumbnailFromPath($data['fileFullPath']);
    //         $data['thumbnailUrl'] = $thumbPath;
    //         $uploadEvent->setData($data);
    //     } catch (FileSystemException $e) {
    //         $uploadEvent->setData(["error" => $e->getMessage()]);
    //         $uploadEvent->setSuccess(false);
    //         $uploadEvent->stopPropagation();
    //     }
    // }
}
