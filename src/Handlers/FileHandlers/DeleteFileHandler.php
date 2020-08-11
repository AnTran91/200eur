<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers\FileHandlers;

use App\Exception\FileSystemException;
use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\ParameterBag;

class DeleteFileHandler extends BaseFileHandler
{
    /**
     * Process an delete.
     *
     * @param string $pictureDir The current picture directory.
     *
     * @return bool
     */
    public function removeFileOrDirectory(string $pictureDir): bool
    {
        $target = substr($this->uploadDirectory, 0, strpos($this->uploadDirectory, $this->uriPrefix)).$pictureDir;

        return $this->storage->doRemove($target);
    }

    /**
     * Process file delete from renovation choices.
     *
     * @param array   $data         The POST Request Bag.
     *
     * @return  array
     */
    public function removeFieldRenovationPicture(array $data)
    {
        $targetFolder = join(DIRECTORY_SEPARATOR, [$this->fieldRenovationChoicesDirectory , $data['uuid']]);

        if ($this->storage->doRemove($targetFolder)) {
            return ["success" => true, "msg" => $this->translator->trans("uploader.upload.msg.delete")];
        } else {
            throw new FileSystemException($this->translator->trans("uploader.upload.msg.error"));
        }
    }

    /**
     * Process a delete.
     *
     * @param string $userDir
     * @param string $orderDir
     * @param string $pictureDir
     * @param string $uuid          The current user directory.
     *
     * @return array
     */
    public function removeReturnedPicture(string $userDir, string $orderDir, string $pictureDir, string $uuid): array
    {
        $targetFolder = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $userDir, $orderDir, $pictureDir, $this->finishDir, $uuid]);

        if ($this->storage->doRemove($targetFolder)) {
            return ["success" => true, "msg" => $this->translator->trans("uploader.upload.msg.delete"), "uuid" => $uuid];
        } else {
            throw new FileSystemException($this->translator->trans("uploader.upload.msg.error"));
        }
    }

    /**
     * Process a delete.
     *
     * @param string        $userDir
     * @param string        $orderDir
     * @param string        $pictureDir
     *
     * @return array
     */
    public function removePictureFromOrder(string $userDir, string $orderDir, string $pictureDir): array
    {
        $targetDir = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $userDir, $orderDir, $pictureDir]);

        if ($this->storage->doRemove($targetDir)) {
            return ["success" => true, "msg" => $this->translator->trans("uploader.upload.msg.delete"), "uuid" => $pictureDir];
        } else {
            throw new FileSystemException($this->translator->trans("uploader.upload.msg.delete_error"));
        }
    }

    /**
     * Process a delete from the user temporary directory.
     *
     * @param ParameterBag  $request    The POST Request Bag.
     * @param string        $uuid       The current user directory.
     *
     * @return array
     */
    public function removePictureFromTmpDir(ParameterBag $request, string $uuid): array
    {
        $this->configUploadDir($uuid);

        if (!$request->has($this->uuid)) {
            throw new ValidationException($this->translator->trans("uploader.validation.params"));
        }

        if ($this->storage->doRemove(join(DIRECTORY_SEPARATOR, array($this->currentUserTmpDirectory, $request->get($this->uuid))))) {
            return ["success" => true, "msg" => $this->translator->trans("uploader.upload.msg.delete"), "uuid" => $request->get($this->uuid)];
        } else {
            throw new FileSystemException($this->translator->trans("uploader.upload.msg.delete_error"));
        }
    }

    /**
     * Process multiple a delete from the user temporary directory.
     *
     * @param ParameterBag  $request    The POST Request Bag.
     * @param string        $uuid       The current user directory.
     *
     * @return array
     */
    public function removeMultiplePictureFromTmpDir(ParameterBag $request, string $uuid): array
    {
        $this->configUploadDir($uuid);

        if (!$request->has($this->multipleUuid) && is_array($request->get($this->multipleUuid))) {
            throw new ValidationException($this->translator->trans("uploader.validation.params"));
        }

        foreach ($request->get($this->multipleUuid) as $pictureDir){
            $this->storage->doRemove(join(DIRECTORY_SEPARATOR, array($this->currentUserTmpDirectory, $pictureDir)));
        }

        return ["success" => true, "msg" => $this->translator->trans("uploader.upload.msg.deleteMultiple")];
    }

    /**
     * Clean up the chunks and tmp folder after using it
     *
     * @param string $uuid
     * @return void
     */
    public function cleanUp(string $uuid): void
    {
        $tempDir = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $uuid, $this->tempDir]);
        $tempChunksFolder = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $uuid, $this->chunksFolder]);

        $this->storage->doCleanupChunks($tempDir);
        $this->storage->doCleanupChunks($tempChunksFolder);
    }
	
	/**
	 * Clean up the chunks folder after using it
	 *
	 * @param array $data
	 * @param string $uuid
	 *
	 * @return void
	 */
	public function cleanUpChunksDir(array $data, string $uuid): void
	{
		$totalParts = $data[$this->totalParts] ?? 0;
		$totalFileSize = $data[$this->totalFileSize] ?? 0;
		
		$targetFolder = join(DIRECTORY_SEPARATOR, array($this->uploadDirectory, $uuid, $this->chunksFolder, sprintf('chunk_%d_%d', $totalFileSize, $totalParts)));
		$this->storage->doCleanupChunks($targetFolder);
	}

    /**
     * Clean up directory
     *
     * @param string $tempChunksFolder
     * @return void
     */
    public function cleanupDir(string $tempChunksFolder): void
    {
        $this->storage->doCleanupChunks($tempChunksFolder);
    }
}
