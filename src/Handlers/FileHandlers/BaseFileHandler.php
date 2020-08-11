<?php

/**
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers\FileHandlers;

use Symfony\Component\Translation\TranslatorInterface;
use App\Storage\InterfaceStorage;
use Symfony\Component\Asset\Packages;

class BaseFileHandler
{
    /**
     * @var string Thumbnail parameters
     */
    protected $extension;
    protected $width;
    protected $height;
    protected $flatten;
    protected $resolutionX;
    protected $resolutionY;
    protected $fontPath;
    protected $fontSize;
    protected $backgroundColor;
    protected $color;
	protected $maxSize;
	
	/**
	 * @var array Thumbnail parameters
	 */
	protected $webExtensions;

    /**
     * @var string Request parameters
     */
    protected $inputName;
    protected $fileName;
    protected $uuid;
    protected $multipleUuid;
    protected $totalParts;
    protected $partIndex;
	protected $totalFileSize;

    /**
     * @var string Upload parameters
     */
    protected $chunksFolder;
    protected $uploadDirectory;
    protected $uriPrefix;
    protected $tempDir;
    protected $paramDir;
    protected $finishDir;
    protected $thumbDir;
    protected $watermarkDir;
    protected $paintDir;
    protected $fieldRenovationChoicesDirectory;

    /**
     * @var string
     */
    protected $currentUserTmpDirectory = null;
    protected $currentUserChunksDirectory = null;

    /**
     * @var string
     */
    protected $pdfDir;

    /**
     * @var InterfaceStorage
     */
    protected $storage;

    /**
     * @var TranslatorInterface
     */
    protected $translator;
	
	/**
	 * @var \Imagine\Gmagick\Imagine
	 */
	protected $imagineGD;

    /**
     * @var \Imagine\Imagick\Imagine
     */
    protected $imagick;

    /**
     * @var Packages
     */
    protected $assetsHelper;

    /**
     * @var string
     */
    protected $watermarkImgPath;

    /**
     * @var string
     */
    protected $watermarkResizePath;
	
	/**
	 * Constructor
	 *
	 * @param InterfaceStorage $storage
	 * @param TranslatorInterface $translator
	 * @param array $uploadConfigs
	 * @param array $requestConfigs
	 * @param array $fpdfConfigs
	 * @param array $thumbConfigs
	 */
    public function __construct(InterfaceStorage $storage, TranslatorInterface $translator, array $uploadConfigs, array $requestConfigs, array $fpdfConfigs, array $thumbConfigs, Packages $assetsHelper)
    {
        $this->storage = $storage;
        $this->translator = $translator;
        $this->imagineGD = new \Imagine\Gd\Imagine();
        $this->imagick = new \Imagine\Imagick\Imagine();
        $this->setConfiguration($uploadConfigs, $requestConfigs, $fpdfConfigs, $thumbConfigs);
        $this->assetsHelper = $assetsHelper;
    }

    /**
     * File Uploader configuration
     *
     * @param array $uploadConfigs
     * @param array $requestConfigs
     * @param array $pdfConfigs
     * @param array $thumbConfigs
     */
    protected function setConfiguration(array $uploadConfigs, array $requestConfigs, array $pdfConfigs, array $thumbConfigs): void
    {
        $this->extension = $thumbConfigs['extension'];
        $this->width = $thumbConfigs['width'];
        $this->height = $thumbConfigs['height'];
        $this->flatten = $thumbConfigs['flatten'];
        $this->resolutionX = $thumbConfigs['resolutionX'];
        $this->resolutionY = $thumbConfigs['resolutionY'];
        $this->maxSize = $thumbConfigs['maxSize'];
	    $this->webExtensions = $thumbConfigs['webExtensions'];

        $this->fontPath = $thumbConfigs['drawImg']['fontPath'];
        $this->fontSize = $thumbConfigs['drawImg']['fontSize'];
        $this->backgroundColor = $thumbConfigs['drawImg']['backgroundColor'];
        $this->color = $thumbConfigs['drawImg']['color'];

        $this->inputName = $requestConfigs['inputName'];
        $this->fileName = $requestConfigs['fileName'];
        $this->uuid = $requestConfigs['uuid'];
        $this->multipleUuid = $requestConfigs['multipleUuid'];
        $this->totalParts = $requestConfigs['totalparts'];
	    $this->totalFileSize = $requestConfigs['filesSize'];
        $this->partIndex = $requestConfigs['partindex'];

        $this->chunksFolder = $uploadConfigs['chunksFolder'];
        $this->uploadDirectory = $uploadConfigs['uploadDirectory'];
        $this->uriPrefix = $uploadConfigs['uriPrefix'];
        $this->tempDir = $uploadConfigs['tempDir'];
        $this->thumbDir = $uploadConfigs['thumbDir'];
        $this->watermarkDir = $uploadConfigs['watermarkDir'];
        $this->watermarkImgPath = $uploadConfigs['watermarkImgPath'];
        $this->watermarkResizePath = $uploadConfigs['watermarkResizePath'];
        $this->paramDir = $uploadConfigs['paramDir'];
        $this->finishDir = $uploadConfigs['finishDir'];
        $this->paintDir = $uploadConfigs['paintDir'];
        $this->fieldRenovationChoicesDirectory = $uploadConfigs['fieldRenovationChoicesDirectory'];

        $this->pdfDir = $pdfConfigs['media'];
    }

    /**
     * Set the Current User upload directory.
     *
     * @param string $uuid The cuurent user directory.
     *
     * @return void
     */
    protected function configUploadDir(string $uuid): void
    {
        $this->currentUserTmpDirectory = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $uuid, $this->tempDir]);
        $this->storage->doCreateDirIfNotExists($this->currentUserTmpDirectory);

        $this->currentUserChunksDirectory = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $uuid, $this->chunksFolder]);
        $this->storage->doCreateDirIfNotExists($this->currentUserChunksDirectory);
    }
}
