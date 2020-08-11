<?php

/**
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers\FileHandlers;

use App\Exception\FileSystemException;

class ChunkedFileUploaderHandler extends BaseFileHandler
{
    /**
     * Process chunked file uploads.
     *
     * @param array   $data         The POST Request Bag.
     * @param string  $targetDir    The target directory
     *
     * @return  array
     */
    public function uploadChunkedImg(array $data, string $targetDir): array
    {
        // Get File and name
        $file = !is_null($data['file']) ? $data['file'] : $data['base64'];
	    $uuid = $data['metadata']['uploadUid'];
        $filename = $data['metadata']['fileName'] ?? $file->getClientOriginalName();

        if (!is_null($data['metadata'])) {
            $totalParts = $data['metadata']['totalChunks'] ?? 0;
            $partIndex = $data['metadata']['chunkIndex'];
            $uploaded = ($totalParts - 1 <= $partIndex);

            $targetFolder = join(DIRECTORY_SEPARATOR, [$targetDir, $this->chunksFolder, $uuid]);
            $this->storage->doCreateDirIfNotExists($targetFolder);

            if ($this->storage->doChunkedUpload($targetFolder, $partIndex, $file) && !$uploaded) {
                return ["uploaded" => $uploaded, "fileUid" => $uuid];
            }

            if ($uploaded) {
                // make sure that the directory does not existe
                $newUuid = $this->storage->doGetUniqueTargetPath($targetDir) ;

                $chunksFolder = join(DIRECTORY_SEPARATOR, [$targetDir, $this->chunksFolder]);
                $targetFolder = join(DIRECTORY_SEPARATOR, [$targetDir, $newUuid]);

                $file = $this->storage->doCombineChunks(join(DIRECTORY_SEPARATOR, [$chunksFolder, $uuid]));

                if ($this->storage->doUpload($file, $targetFolder, $filename)) {
                    return [
                    "chunksFolder" => $chunksFolder ,
                    "uploaded" => $uploaded,
                    "fileUid" => $newUuid,
                    "name" => $filename,
                    "fileDir" => $targetFolder,
                    "filePath" => $this->storage->doGetUriPath(join(DIRECTORY_SEPARATOR, [$targetFolder, $filename]), $this->uriPrefix),
                    "fileFullPath" => join(DIRECTORY_SEPARATOR, [$targetFolder, $filename]),
                    "msg" => $this->translator->trans("uploader.upload.msg.new")
                  ];
                }
            }
        } else {
            $targetFolder = join(DIRECTORY_SEPARATOR, [$targetDir, $uuid]);
            $this->storage->doCreateDirIfNotExists($targetFolder);

            if ($this->storage->doUpload($file, $targetFolder, $filename)) {
                return [
                "uploaded" => true,
                "fileUid" => $uuid,
                "name" => $filename,
                "fileDir" => $targetFolder,
                "filePath" => $this->storage->doGetUriPath(join(DIRECTORY_SEPARATOR, [$targetFolder, $filename]), $this->uriPrefix),
                "msg" => $this->translator->trans("uploader.upload.msg.new"),
                "uuid" => $uuid
              ];
            }
        }

        throw new FileSystemException($this->translator->trans("uploader.upload.msg.error"));
    }

    /**
     * Process the upload of one file.
     *
     * @param array   $data         The POST Request Bag.
     * @param string  $userDir      The cuurent user directory
     * @param string  $orderDir     The cuurent order directory
     * @param string  $pictureDir   The cuurent picture directory
     *
     * @return  array
     */
    public function handleOrderPictureUpload(array $data, string $userDir, string $orderDir, ?string $pictureDir): array
    {
        // set the contained dir
        $containedFolder = !is_null($data['file']) ? $this->finishDir : $this->paintDir;
        $targetFolder = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $userDir, $orderDir, $pictureDir, $containedFolder]);

        return $this->uploadChunkedImg($data, $targetFolder);
    }

    /**
     * Process file upload to renovation choices.
     *
     * @param array   $data         The POST Request Bag.
     *
     * @return  array
     */
    public function handleFieldRenovationChoicesUpload(array $data): array
    {
        return $this->uploadChunkedImg($data, $this->fieldRenovationChoicesDirectory);
    }
}
