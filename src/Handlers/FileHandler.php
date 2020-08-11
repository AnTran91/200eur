<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers;

use App\Handlers\FileHandlers as FileHandlers;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ParameterBag;

class FileHandler
{
    /**
     * @var FileHandlers\ChunkedFileUploaderHandler
     */
    private $chunkedFileUploaderHandler;

    /**
     * @var FileHandlers\DeleteFileHandler
     */
    private $deleteFileHandler;

    /**
     * @var FileHandlers\DirectoryHandler
     */
    private $directoryHandler;

    /**
     * @var FileHandlers\FineUploaderHandler
     */
    private $fineUploaderHandler;

    /**
     * @var FileHandlers\ImmosquareFileHandler
     */
    private $immosquareFileHandler;

    /**
     * @var FileHandlers\ParamHandler
     */
    private $paramHandler;

    /**
     * @var FileHandlers\ThumbnailHandler
     */
    private $thumbnailHandler;

    /**
     * @var FileHandlers\WatermarkHandler
     */
    private $watermarkHandler;

    /**
     * FileHandler constructor.
     *
     * @param FileHandlers\ChunkedFileUploaderHandler   $chunkedFileUploaderHandler
     * @param FileHandlers\DeleteFileHandler            $deleteFileHandler
     * @param FileHandlers\DirectoryHandler             $directoryHandler
     * @param FileHandlers\FineUploaderHandler          $fineUploaderHandler
     * @param FileHandlers\ImmosquareFileHandler        $immosquareFileHandler
     * @param FileHandlers\ParamHandler                 $paramHandler
     * @param FileHandlers\ThumbnailHandler             $thumbnailHandler
     */
    public function __construct(FileHandlers\ChunkedFileUploaderHandler $chunkedFileUploaderHandler, FileHandlers\DeleteFileHandler $deleteFileHandler, FileHandlers\DirectoryHandler $directoryHandler, FileHandlers\FineUploaderHandler $fineUploaderHandler, FileHandlers\ImmosquareFileHandler $immosquareFileHandler, FileHandlers\ParamHandler $paramHandler, FileHandlers\ThumbnailHandler $thumbnailHandler, FileHandlers\WatermarkHandler $watermarkHandler)
    {
        $this->chunkedFileUploaderHandler = $chunkedFileUploaderHandler;
        $this->deleteFileHandler = $deleteFileHandler;
        $this->directoryHandler = $directoryHandler;
        $this->fineUploaderHandler = $fineUploaderHandler;
        $this->immosquareFileHandler = $immosquareFileHandler;
        $this->paramHandler = $paramHandler;
        $this->thumbnailHandler = $thumbnailHandler;
        $this->watermarkHandler = $watermarkHandler;
    }

    public function uploadChunkedImg(array $data, string $targetDir): array
    {
        return $this->chunkedFileUploaderHandler->uploadChunkedImg($data, $targetDir);
    }

    public function handleOrderPictureUpload(array $data, ?string $userDir, ?string $orderDir, ?string $pictureDir): array
    {
        return $this->chunkedFileUploaderHandler->handleOrderPictureUpload($data, $userDir, $orderDir, $pictureDir);
    }

    public function handleFieldRenovationChoicesUpload(array $data): array
    {
        return $this->chunkedFileUploaderHandler->handleFieldRenovationChoicesUpload($data);
    }

    public function removeFileOrDirectory(string $pitureDir): bool
    {
        return $this->deleteFileHandler->removeFileOrDirectory($pitureDir);
    }

    public function removeFieldRenovationPicture(array $data): array
    {
        return $this->deleteFileHandler->removeFieldRenovationPicture($data);
    }

    public function removeReturnedPicture(string $userDir, string $orderDir, string $pictureDir, string $returnedPictureDirectory): array
    {
        return $this->deleteFileHandler->removeReturnedPicture($userDir, $orderDir, $pictureDir, $returnedPictureDirectory);
    }

    public function removePictureFromOrder(string $userDir, string $orderDir, string $pictureDir): array
    {
        return $this->deleteFileHandler->removePictureFromOrder($userDir, $orderDir, $pictureDir);
    }

    public function removePictureFromTmpDir(ParameterBag $request, string $userDir): array
    {
        return $this->deleteFileHandler->removePictureFromTmpDir($request, $userDir);
    }

    public function removeMultiplePictureFromTmpDir(ParameterBag $request, string $userDir): array
    {
        return $this->deleteFileHandler->removeMultiplePictureFromTmpDir($request, $userDir);
    }

    public function cleanUp(string $userDir): void
    {
        $this->deleteFileHandler->cleanUp($userDir);
    }

    public function cleanupDir(string $tempChunksFolder): void
    {
        $this->deleteFileHandler->cleanupDir($tempChunksFolder);
    }
	
	public function cleanUpChunksDir(array $data, string $uuid): void
	{
		$this->deleteFileHandler->cleanUpChunksDir($data, $uuid);
	}

    public function doGenerateThumbnail(string $fileFullPath, string $imgDir, string $fileName): string
    {
        return $this->thumbnailHandler->doGenerateThumbnail($fileFullPath, $imgDir, $fileName);
    }

    public function doGenerateThumbnailFromPath(string $imgPath): string
    {
        return $this->thumbnailHandler->doGenerateThumbnailFromPath($imgPath);
    }

    public function doGenerateWatermarked(string $fileFullPath, string $imgDir, string $fileName): string
    {
        return $this->watermarkHandler->doGenerateWatermarked($fileFullPath, $imgDir, $fileName);
    }

    public function doGenerateGifWatermarked(string $fileFullPath, string $imgDir, string $fileName): string
    {
        return $this->watermarkHandler->doGenerateGifWatermarked($fileFullPath, $imgDir, $fileName);
    }

    public function doGenerateMP4Watermarked(string $fileFullPath, string $imgDir, string $fileName): string
    {
        return $this->watermarkHandler->doGenerateMP4Watermarked($fileFullPath, $imgDir, $fileName);
    }

    public function uploadParamFile(UploadedFile $file, ?string $filename, string $userDir, string $pictureDir, ?string $orderDir = null): string
    {
        return $this->paramHandler->uploadParamFile($file, $filename, $userDir, $pictureDir, $orderDir);
    }

    public function getOrderUniqueDir(string $userDir): string
    {
        return $this->immosquareFileHandler->getOrderUniqueDir($userDir);
    }

    public function createOrderStructure(array $body, string $userDir): array
    {
        return $this->immosquareFileHandler->createOrderStructure($body, $userDir);
    }

    public function combineChunks(array $data, string $userDir): array
    {
        return $this->fineUploaderHandler->combineChunks($data, $userDir);
    }

    public function handleUpload(array $data, string $userDir): array
    {
        return $this->fineUploaderHandler->handleUpload($data, $userDir);
    }

    public function moveFiles(string $userDir): string
    {
        return $this->directoryHandler->moveFiles($userDir);
    }

    public function moveFileFromTmpDirToOrderDir(string $userDir, string $pictureDir, string $filename, string $thumbFilename, string $orderDir): array
    {
        return $this->directoryHandler->moveFileFromTmpDirToOrderDir($userDir, $pictureDir, $filename, $thumbFilename, $orderDir);
    }

    public function doGetRealPath(string $path): string
    {
        return $this->directoryHandler->doGetRealPath($path);
    }

    public function initTmpFiles(?array $sessionData, string $userDir): array
    {
        return $this->directoryHandler->initTmpFiles($sessionData, $userDir);
    }

    public function initTmpFile(?array $sessionData, string $userDir, string $pictureDir): ?array
    {
        return $this->directoryHandler->initTmpFile($sessionData, $userDir, $pictureDir);
    }

    public function getDirFiles(?array $sessionData, string $userDir, string $dirName): array
    {
        return $this->directoryHandler->getDirFiles($sessionData, $userDir, $dirName);
    }

    public function getUploadedFilesNumber(string $userDir): int
    {
        return $this->directoryHandler->getUploadedFilesNumber($userDir);
    }

    public function getTmpDir(): string
    {
        return $this->directoryHandler->getTmpDir();
    }

    public function copy(string $originFile, string $targetFile): void
    {
        $this->directoryHandler->copy($originFile, $targetFile);
    }

    public function getUserUniqueDir(string $username): string
    {
        return $this->directoryHandler->getUserUniqueDir($username);
    }

    public function isFile(?string $pictureDir):bool
    {
        return $this->directoryHandler->isFile($pictureDir);
    }
}
