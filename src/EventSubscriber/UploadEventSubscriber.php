<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use App\Events\UploadEvent;
use App\Utils\Events;

use App\Entity\Picture;

use Doctrine\ORM\EntityManagerInterface;

use App\Handlers\OrderHandler;
use App\Handlers\FileHandler;

use App\Exception\FileSystemException;

/**
 * @see \App\Events\UploadEvent class.
 */
final class UploadEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var FileHandler
     */
    private $fileHandler;

    /**
     * @var OrderHandler
     */
    private $orderHandler;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Constructor
     *
     * @param FileHandler $uploader
     * @param OrderHandler $orderHandler
     * @param EntityManagerInterface $em
     */
    public function __construct(FileHandler $uploader, OrderHandler $orderHandler, EntityManagerInterface $em)
    {
        $this->fileHandler = $uploader;
        $this->orderHandler = $orderHandler;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
          Events::ON_UPLOAD => [
            array('onUpload', 0),
            array('onPostGenerateThumb', -10),
            array('onCreateNewPicture', -20)
          ],
          Events::ON_CHUNK_UPLOAD => [
            array('onChunkUpload', 0),
            array('onPostGenerateThumb', -10),
            array('onCreateNewPicture', -20),
	        array('onClearChunksDir', -30),
	         
          ],
          Events::ON_DELETE_UPLOADED_FILE => [
            array('onDelete', 0)
          ],
          Events::ON_DELETE_SAVED_FILE => [
            array('onDeletePicture', 0),
            array('onUpdateOrderDetails', -10)
          ],
          Events::ON_DELETE_MULTIPLE_UPLOADED_FILE => array('onMultipleDelete', 0)
        ];
    }

    /**
     * do handle Upload
     *
     * @param UploadEvent $uploadEvent
     */
    public function onUpload(UploadEvent $uploadEvent)
    {
        try {
            if (!$uploadEvent->getForm()->isSubmitted() || !$uploadEvent->getForm()->isValid()) {
                throw new FileSystemException(\App\Utils\Tools::getErrorMessages($uploadEvent->getForm()));
            }

            $uploadedFile = $this->fileHandler->handleUpload($uploadEvent->getForm()->getData(), $uploadEvent->getTargetFolder());
            $uploadEvent->setData($uploadedFile);
            $uploadEvent->setStatusCode(true);
        } catch (FileSystemException $e) {
            $uploadEvent->setData(["error" => $e->getMessage()]);
            $uploadEvent->setStatusCode(false);
            $uploadEvent->stopPropagation();
        }
    }

    /**
     * Event Object is passed as method argument
     *
     * @param UploadEvent $uploadEvent
     */
    public function onChunkUpload(UploadEvent $uploadEvent)
    {
        try {
            if (!$uploadEvent->getForm()->isSubmitted() || !$uploadEvent->getForm()->isValid()) {
                throw new FileSystemException(\App\Utils\Tools::getErrorMessages($uploadEvent->getForm()));
            }

            $uploadedFile = $this->fileHandler->combineChunks($uploadEvent->getForm()->getData(), $uploadEvent->getTargetFolder());

            $uploadEvent->setData($uploadedFile);
            $uploadEvent->setStatusCode(true);
        } catch (FileSystemException $e) {
            $uploadEvent->setData(["error" => $e->getMessage()]);
            $uploadEvent->setStatusCode(false);
            $uploadEvent->stopPropagation();
        }
    }

    /**
     * Event Object is passed as method argument
     *
     * @param UploadEvent $uploadEvent
     */
    public function onDelete(UploadEvent $uploadEvent)
    {
        $request = $uploadEvent->getRequest();
        try {
            $uploadedFile = $this->fileHandler->removePictureFromTmpDir($request->request, $uploadEvent->getTargetFolder());

            $uploadEvent->setData($uploadedFile);
            $uploadEvent->setStatusCode(true);
        } catch (FileSystemException $e) {
            $uploadEvent->setData(["error" => $e->getMessage()]);
            $uploadEvent->setStatusCode(false);
            $uploadEvent->stopPropagation();
        }
    }

    /**
     * Event Object is passed as method argument
     *
     * @param UploadEvent $uploadEvent
     */
    public function onMultipleDelete(UploadEvent $uploadEvent)
    {
        $request = $uploadEvent->getRequest();
        try {
            $uploadedFile = $this->fileHandler->removeMultiplePictureFromTmpDir($request->request, $uploadEvent->getTargetFolder());

            $uploadEvent->setData($uploadedFile);
            $uploadEvent->setStatusCode(true);
        } catch (FileSystemException $e) {
            $uploadEvent->setData(["error" => $e->getMessage()]);
            $uploadEvent->setStatusCode(false);
            $uploadEvent->stopPropagation();
        }
    }

    /**
     * Generate thumbnail after the upload complete
     *
     * @param UploadEvent $uploadEvent
     */
    public function onPostGenerateThumb(UploadEvent $uploadEvent)
    {
        $uploadedFile = $uploadEvent->getData();

        if (!array_key_exists('newUuid', $uploadedFile) || !array_key_exists('newName', $uploadedFile) || !array_key_exists('fileFullPath', $uploadedFile)) {
            return;
        }

        try {
            $thumbPath = $this->fileHandler->doGenerateThumbnail($uploadedFile['fileFullPath'], $uploadedFile['fileDir'], $uploadedFile['newName']);
            $uploadedFile['thumbnailUrl'] = $thumbPath;
            $uploadedFile['thumbnailFileName'] = basename($thumbPath);
            $uploadEvent->setData($uploadedFile);
        } catch (FileSystemException $e) {
            $uploadEvent->setData(["error" => $e->getMessage()]);
            $uploadEvent->setStatusCode(false);
            $uploadEvent->stopPropagation();
        }
    }

    /**
     * Save picture in the database
     *
     * @param UploadEvent $uploadEvent
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function onCreateNewPicture(UploadEvent $uploadEvent)
    {
        try {
            $uploadedFile = $uploadEvent->getData();
            $order = $uploadEvent->getOrder();

            if (is_null($order) || !array_key_exists('newUuid', $uploadedFile) || !array_key_exists('newName', $uploadedFile) || !array_key_exists('thumbnailFileName', $uploadedFile)) {
                return;
            }

            $data = $this->fileHandler->moveFileFromTmpDirToOrderDir($uploadEvent->getTargetFolder(), $uploadedFile['newUuid'], $uploadedFile['newName'], $uploadedFile['thumbnailFileName'], $order->getUploadFolder());
            $picture = (new Picture())
                  ->setPictureName($data["name"] ?? null)
                  ->setPicturePath($data["real_path"])
                  ->setPicturePathThumb($data["thumbnailUrl"])
                  ->setPictureDirectory($data["uuid"] ?? null)
                  ->setOrder($order);

            $this->em->persist($picture);
            $this->em->flush();

            $uploadedFile['newUuid'] = $picture->getId();
            $uploadEvent->setData($uploadedFile);
        } catch (\Exception $e) {
            $this->em->getConnection()->rollback();
            $uploadEvent->setData(["error" => $e->getMessage()]);
            $uploadEvent->setStatusCode(false);
            $uploadEvent->stopPropagation();
        }
    }

    /**
     * Event Object is passed as method argument
     *
     * @param UploadEvent $uploadEvent
     */
    public function onDeletePicture(UploadEvent $uploadEvent)
    {
        try {
            $request = $uploadEvent->getRequest();
            $order = $uploadEvent->getOrder();

            $picture = $this->em->getRepository(Picture::class)
                ->findOneBy(['id' => $request->request->get('qquuid')]);

            $uploadedFile = $this->fileHandler->removePictureFromOrder($uploadEvent->getTargetFolder(), $order->getUploadFolder(), $picture->getPictureDirectory());

            $uploadEvent->setData($uploadedFile);
            $uploadEvent->setStatusCode(true);

            $order->removePicture($picture);
            $picture->setOrder(null);

            $this->em->remove($picture);

            $this->em->flush();
        } catch (FileSystemException $e) {
            $uploadEvent->setData(["error" => $e->getMessage()]);
            $uploadEvent->setStatusCode(false);
            $uploadEvent->stopPropagation();
        }
    }

    /**
     * on Update OrderCreation Details
     *
     * @param UploadEvent $uploadEvent
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function onUpdateOrderDetails(UploadEvent $uploadEvent)
    {
        $order = $uploadEvent->getOrder();
        $this->orderHandler->updateOrderPrice($order);
        $this->em->flush();
    }
	
	/**
	 * After OrderCreation persist
	 *
	 * @param UploadEvent $uploadEvent
	 */
	public function onClearChunksDir(UploadEvent $uploadEvent)
	{
		$uploadedFile = $uploadEvent->getData();
		
		if (!array_key_exists('newUuid', $uploadedFile) || !array_key_exists('newName', $uploadedFile) || !array_key_exists('thumbnailFileName', $uploadedFile)) {
			return;
		}
		
		$this->fileHandler->cleanUpChunksDir($uploadEvent->getForm()->getData(), $uploadEvent->getTargetFolder());
	}
}
