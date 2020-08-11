<?php

/**
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Storage;

use App\Utils\FileSystemLibrary;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * File System Storage.
 */
class FileSystemStorage implements InterfaceStorage
{
    /**
     * @var \App\Utils\FileSystemLibrary
     */
    private $fileSystem;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fileSystem = new FileSystemLibrary();
    }

    /**
     * {@inheritDoc}
     */
    public function doUpload(UploadedFile $file, string $dir, string $name= null): bool
    {
        if ($name === null) {
            $name = $file->getClientOriginalName();
        }

        return $this->fileSystem->moveUploadedFile($file, $dir, $name);
    }

    /**
     * {@inheritDoc}
     */
    public function doMoveFiles(string $originDir, string $targetDir):void
    {
        $this->fileSystem->move($originDir, $targetDir);
    }

    /**
     * {@inheritDoc}
     */
    public function doCopy(string $originFile, string $targetFile): void
    {
        $this->fileSystem->copyFile($originFile, $targetFile);
    }

    /**
     * {@inheritDoc}
     */
    public function doRemove(string $dir): bool
    {
        if ($this->fileSystem->exists($dir)) {
            return $this->fileSystem->delete($dir);
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function doCreateDirIfNotExists(string $dir): void
    {
        if (!$this->doExiste($dir)) {
            $this->fileSystem->createFolder($dir);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function doChunkedUpload(string $targetFolder, string $index, UploadedFile $chunk): bool
    {
        return $this->fileSystem->addChunk($targetFolder, $index, $chunk);
    }

    /**
     * {@inheritDoc}
     */
    public function doCombineChunks(string $chunksFolder): UploadedFile
    {
        $chunks = $this->fileSystem->getChunks($chunksFolder);

        return $this->fileSystem->assembleChunks($chunks, false);
    }

    /**
     * {@inheritDoc}
     */
    public function doInitFiles(?string $dir, ?string $uriPrefix, ?array $sessionData, ?array $exclude, ?string $thumbDir): ?array
    {
        $initialFiles = array();
        
        foreach ($this->fileSystem->getDirContent($dir, $exclude, $thumbDir) as $content) {
            if (!array_key_exists("thumb_path", $content) || $content["thumb_path"] === "") {
                $initialFile["thumbnailUrl"] = $this->doGetUriPath($content["real_path"], $uriPrefix);
            } else {
                $initialFile["thumbnailUrl"] = $this->doGetUriPath($content["thumb_path"], $uriPrefix);
            }

            $initialFile["param"] = $sessionData['params'][$content["directory"]] ?? [];
            $initialFile["retouch"] = $sessionData['retouchs'][$content["directory"]] ?? [];
            $initialFile["uuid"] = $content["directory"];
            $initialFile["name"] = $content["filename"];
            $initialFile["size"] = $content["size"];
            $initialFile["real_path"] = $this->doGetUriPath($content["real_path"], $uriPrefix);

            array_push($initialFiles, $initialFile);
        }

        return $initialFiles;
    }

    /**
     * {@inheritDoc}
     */
    public function doCreateZIP(?string $dirPath, ?string $zipPath, ?string $zipName, ?array $exclude): string
    {
        return $this->fileSystem->createZIP($dirPath, $zipPath, $zipName, $exclude);
    }

    /**
     * {@inheritDoc}
     */
    public function doInitFile(?string $dir, ?string $uriPrefix, ?array $sessionData, ?array $exclude, ?string $thumbDir): ?array
    {
        $content = $this->fileSystem->getFileContent($dir, $exclude, $thumbDir);
        $default = $sessionData['params']['saveForAll'] ?? null;

        if (!array_key_exists("thumb_path", $content) || $content["thumb_path"] === "") {
            $initialFile["thumbnailUrl"] = $this->doGetUriPath($content["real_path"], $uriPrefix);
        } else {
            $initialFile["thumbnailUrl"] = $this->doGetUriPath($content["thumb_path"], $uriPrefix);
        }

        $initialFile["param"] = $sessionData['params'][$content["directory"]] ?? $default;
        $initialFile["retouch"] = $sessionData['retouchs'][$content["directory"]] ?? [];
        $initialFile["uuid"] = $content["directory"];
        $initialFile["name"] = $content["filename"];
        $initialFile["size"] = $content["size"];
        $initialFile["real_path"] = $this->doGetUriPath($content["real_path"], $uriPrefix);

        return $initialFile;
    }

    /**
     * {@inheritDoc}
     */
    public function doGetUniqueTargetPath(string $uploadDirectory, ?string $uuid = null): string
    {
        return $this->fileSystem->getUniqueTargetPath($uploadDirectory, $uuid);
    }

    /**
     * {@inheritDoc}
     */
    public function doGetAllUploadedFilesNumber(string $dir): int
    {
        return $this->fileSystem->getTempDirFilesNumber($dir);
    }

    /**
     * {@inheritDoc}
     */
    public function doExiste(string $dir): bool
    {
        return $this->fileSystem->exists($dir);
    }

    /**
     * {@inheritDoc}
     */
    public function doCleanupChunks(string $targetFolder): void
    {
        $this->fileSystem->cleanupChunks($targetFolder);
    }

    /**
     * {@inheritDoc}
     */
    public function doGetUriPath(string $path, string $uriPrefix): string
    {
        return substr($path, strpos($path, $uriPrefix));
    }

    /**
     * {@inheritDoc}
     */
    public function doRemoveUriPath(string $path, string $uriPrefix): string
    {
        return substr($path, 0, strpos($path, $uriPrefix));
    }

    /**
     * {@inheritDoc}
     */
    public function doGetRealPath(string $path, string $uriPrefix): string
    {
        return substr($path, strpos($path, $uriPrefix));
    }

    /**
     * {@inheritDoc}
     */
    public function doGetUniqueUserDirectory(string $uploadDirectory, string $username): string
    {
        return $this->fileSystem->getUniqueUserDirectory($uploadDirectory, $username);
    }
}
