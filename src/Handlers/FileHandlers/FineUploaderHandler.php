<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers\FileHandlers;

use App\Exception\FileSystemException;

class FineUploaderHandler extends BaseFileHandler
{
    /**
     * Combine Chunks.
     *
     * @param array  $data    The POST Request Bag.
     * @param string $uuid    The current user directory
     *
     * @return array
     */
    public function combineChunks(array $data, string $uuid): array
    {
        $this->configUploadDir($uuid);
	
	    // Save a chunk
	    $totalParts = $data[$this->totalParts] ?? 0;
	    $totalFileSize = $data[$this->totalFileSize] ?? 0;
	    
        // make sure that the directory does not exists
        $uuid = $this->storage->doGetUniqueTargetPath($this->currentUserTmpDirectory, $data[$this->uuid]) ;

        $chunksFolder = join(DIRECTORY_SEPARATOR, array($this->currentUserChunksDirectory, sprintf('chunk_%d_%d', $totalFileSize, $totalParts)));
        $targetFolder = join(DIRECTORY_SEPARATOR, array($this->currentUserTmpDirectory, $uuid));

        $file = $this->storage->doCombineChunks($chunksFolder);
        $filename =  $data[$this->fileName] ?? $file->getClientOriginalName();

        if ($this->storage->doUpload($file, $targetFolder, $filename, true)) {
            return ["newName" => $filename, "msg" => $this->translator->trans("uploader.upload.msg.new"), "success" => true, "newUuid" => $uuid, "fileDir" => $targetFolder, "fileFullPath" => join(DIRECTORY_SEPARATOR, [$targetFolder, $filename])];
        }

        throw new FileSystemException($this->translator->trans("uploader.upload.msg.error"));
    }

    /**
     * Process the upload.
     *
     * @param array   $data    The POST Request Bag.
     * @param string  $uuid    The current user directory
     *
     * @return  array
     */
    public function handleUpload(array $data, string $uuid): array
    {
        $this->configUploadDir($uuid);
        // Get size and name
        $file = $data[$this->inputName];
	    $filename = $data['metadata']['fileName'] ?? $file->getClientOriginalName();

        // Save a chunk
        $totalParts = $data[$this->totalParts] ?? 0;
	    $totalFileSize = $data[$this->totalFileSize] ?? 0;

        if ($totalParts > 0) {
            $uuid = $data[$this->uuid];
            $partIndex = $data[$this->partIndex];

            $targetFolder = join(DIRECTORY_SEPARATOR, array($this->currentUserChunksDirectory, sprintf('chunk_%d_%d', $totalFileSize, $totalParts)));

            if ($this->storage->doChunkedUpload($targetFolder, $partIndex, $file)) {
                return ["success" => true, "uuid" => $uuid];
            }
        } else {
            // make sure that the directory does not existe
            $uuid = $this->storage->doGetUniqueTargetPath($this->currentUserTmpDirectory, $data[$this->uuid]) ;

            $target = join(DIRECTORY_SEPARATOR, array($this->currentUserTmpDirectory, $uuid));

            if ($this->storage->doUpload($file, $target, $filename, true)) {
                return ["newName" => $filename, "msg" => $this->translator->trans("uploader.upload.msg.new"), "success" => true, "newUuid" => $uuid, "fileDir" => $target, "fileFullPath" => join(DIRECTORY_SEPARATOR, [$target, $filename])];
            }
        }
        throw new FileSystemException($this->translator->trans("uploader.upload.msg.error"));
    }
}
