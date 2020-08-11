<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers;

use App\Entity\Order;

use App\Storage\InterfaceStorage;

use ZipArchive;
use SplFileInfo;

class OrderArchiveHandler
{
    /**
     * @var string Upload parameters
     */
    private $uploadDirectory;
    private $uriPrefix;
    private $publicDirPath;
    private $tempDir;
    private $paramDir;
    private $finishDir;
    private $thumbDir;
	private $zipDir;

    /**
     * @var InterfaceStorage
     */
    private $storage;
	
	/**
	 * Constructor
	 *
	 * @param InterfaceStorage $storage
	 * @param array $uploadConfigs
	 */
    public function __construct(InterfaceStorage $storage, array $uploadConfigs)
    {
        $this->storage = $storage;
        $this->setConfiguration($uploadConfigs);
    }

    /**
     * File Uploader configuration
     *
     * @param array $uploadConfigs
     */
    public function setConfiguration(array $uploadConfigs): void
    {
        $this->uploadDirectory = $uploadConfigs['uploadDirectory'];
        $this->uriPrefix = $uploadConfigs['uriPrefix'];
        $this->publicDirPath = $uploadConfigs['publicDirPath'];
        $this->tempDir = $uploadConfigs['tempDir'];
        $this->paramDir = $uploadConfigs['paramDir'];
        $this->zipDir = $uploadConfigs['zipDir'];
        $this->thumbDir = $uploadConfigs['thumbDir'];
        $this->finishDir = $uploadConfigs['finishDir'];
    }

    /**
     * Create ZIP file of Returned images
     *
     * @param Order $order
     *
     * @return string ZIP path
     */
    public function createDoneArchive(Order $order): string
    {
        $zipPath = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $order->getClient()->getUserDirectory(), $order->getUploadFolder(), $this->zipDir]);
        $this->storage->doCreateDirIfNotExists($zipPath);

        $zipDir = new SplFileInfo(join(DIRECTORY_SEPARATOR, [$zipPath, sprintf('done_%04d.zip', $order->getOrderNumber() ?? 0)]));

        $archive = new ZipArchive();
        $archive->open($zipDir->getPathname(), ZipArchive::CREATE|ZipArchive::OVERWRITE|\ZipArchive::CHECKCONS);

        foreach ($order->getPictures() as $picture) {
            foreach ($picture->getPictureDetail() as $pictureDetail) {
                if (is_null($pictureDetail->getReturnedPicture())) {
                    continue;
                }
				
                $elem = new SplFileInfo($this->storage->doRemoveUriPath($this->uploadDirectory, $this->uriPrefix).$pictureDetail->getReturnedPicture()->getPicturePath());
                
                $filename = $pictureDetail->getReturnedPicture()->getPictureName();
                $archive->addFile($elem->getPathname(),$filename);
                
            }
        }
        $archive->close();

        return $zipDir->getPathname();
    }

    /**
     * Create ZIP file of Illustrative images
     *
     * @param Order $order
     *
     * @return string ZIP path
     */
    public function createIllustrativeArchive(Order $order): string
    {
        $zipPath = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $order->getClient()->getUserDirectory(), $order->getUploadFolder(), $this->zipDir]);

        $this->storage->doCreateDirIfNotExists($zipPath);

        $zipDir = new SplFileInfo(join(DIRECTORY_SEPARATOR, [$zipPath, sprintf('illusImage_%d.zip', $order->getOrderNumber() ?? 0)]));

        $archive = new ZipArchive();
        $archive->open($zipDir->getPathname(), ZipArchive::CREATE|ZipArchive::OVERWRITE|\ZipArchive::CHECKCONS);
        if ($order->getPictures() !== null) {
            foreach ($order->getPictures() as $picture) {
                if ($picture->getPictureDetail() !== null) {
                    $count = 0;
                    foreach ($picture->getPictureDetail() as $pictureDetail) {
                        if (is_null($pictureDetail->getReturnedPicture())) {
                            continue;
                        }
                        if ($pictureDetail->getReturnedPicture()->getPaintedPicturePath() !== null) {
                            $elem = new SplFileInfo($this->storage->doRemoveUriPath($this->uploadDirectory, $this->uriPrefix).$pictureDetail->getReturnedPicture()->getPaintedPicturePath());
                            $pathInfo = pathinfo($pictureDetail->getReturnedPicture()->getPaintedPicturePath());
                            $filename = base64_encode($pathInfo['filename']) . "-" . $count . "." . $pathInfo['extension'];
                            $archive->addFile($elem->getPathname(), $filename);
                            $count++;
                        }
                    }
                }
            }
        }
        $archive->close();
        return $zipDir->getPathname();
    }

    /**
     * Create ZIP file of client images
     *
     * @param Order $order
     *
     * @return string ZIP path
     */
    public function createToDoArchive(Order $order): string
    {
        $zipPath = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $order->getClient()->getUserDirectory(), $order->getUploadFolder(), $this->zipDir]);
        $this->storage->doCreateDirIfNotExists($zipPath);

        $zipDir = new SplFileInfo(join(DIRECTORY_SEPARATOR, [$zipPath, sprintf('todo_%04d.zip', $order->getOrderNumber() ?? 0)]));

        $archive = new ZipArchive();
        $archive->open($zipDir->getPathname(), ZipArchive::CREATE|ZipArchive::OVERWRITE|\ZipArchive::CHECKCONS);
	
        foreach ($order->getPictures() as $picture) {
	        $filename = sprintf('(%04d) %s', $picture->getId(),$picture->getPictureName());
	
	        if (filter_var( preg_replace('/\\?.*/', '', $picture->getPicturePath()), FILTER_VALIDATE_URL)) {
		        $downloadFile = file_get_contents($picture->getPicturePath());
		        $archive->addFromString($filename, $downloadFile);
	        }else{
		        $elem = new SplFileInfo($this->storage->doRemoveUriPath($this->uploadDirectory, $this->uriPrefix).$picture->getPicturePath());
		        $archive->addFile($elem->getRealPath(),$filename);
	        }
        }
        $archive->close();

        return $zipDir->getRealPath();
    }
}
