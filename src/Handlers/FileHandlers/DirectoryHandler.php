<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers\FileHandlers;

class DirectoryHandler extends BaseFileHandler
{
    /**
     * Move a directory to another.
     *
     * @param string $uuid
     * @return string
     */
    public function moveFiles(string $uuid): string
    {
        $dirPath = $this->storage->doGetUniqueTargetPath(join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $uuid]));

        $originDir = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $uuid, $this->tempDir]);
        $targetDir = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $uuid, $dirPath]);

        $this->storage->doMoveFiles($originDir, $targetDir);

        return $dirPath;
    }

    /**
     * Move a directory to another.
     *
     * @param string $userDir
     * @param string $pictureDir
     * @param string $filename
     * @param string $thumbFilename
     * @param string $orderDir
     * @return array
     */
    public function moveFileFromTmpDirToOrderDir(string $userDir, string $pictureDir, string $filename, string $thumbFilename, string $orderDir): array
    {
        // make sure that the directory does not existe
        $uuid = $this->storage->doGetUniqueTargetPath(join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $userDir, $orderDir]), $pictureDir) ;

        $originDir = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $userDir, $this->tempDir, $pictureDir]);
        $targetDir = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $userDir, $orderDir, $uuid]);

        $this->storage->doCreateDirIfNotExists($targetDir);
        $this->storage->doMoveFiles($originDir, $targetDir);
        $this->storage->doRemove($originDir);

        return [
        "uuid" => $pictureDir,
        "name" => $filename,
        "real_path" => $this->storage->doGetUriPath(join(DIRECTORY_SEPARATOR, array($targetDir, $filename)), $this->uriPrefix),
        "thumbnailUrl" => $this->storage->doGetUriPath(join(DIRECTORY_SEPARATOR, array($targetDir, $this->thumbDir, $thumbFilename)), $this->uriPrefix)
      ];
    }

    /**
     * Get File Real Path
     *
     * @param string $path
     *
     * @return string
     */
    public function doGetRealPath(string $path): string
    {
        return join(DIRECTORY_SEPARATOR, array(substr($this->uploadDirectory, 0, strpos($this->uploadDirectory, $this->uriPrefix)), $path));
    }

    /**
     * Return all  tmp the Uploaded Files
     *
     * @param array   $sessionData  The session data.
     * @param string  $uuid         The cuurent user directory.
     *
     * @return array
     */
    public function initTmpFiles(?array $sessionData, string $uuid): array
    {
        $currentUserTmpDirectory = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $uuid, $this->tempDir]);
        $excludeDirs = [$this->paramDir];
        $this->storage->doCreateDirIfNotExists($currentUserTmpDirectory);
	
	    
        return $this->storage->doInitFiles($currentUserTmpDirectory, $this->uriPrefix, $sessionData, $excludeDirs, $this->thumbDir);
    }

    /**
     * Return one tmp File
     *
     * @param array   $sessionData  The session data.
     * @param string  $uuid         The cuurent user directory.
     * @param string  $dir          The cuurent picture directory.
     *
     * @return array
     */
    public function initTmpFile(?array $sessionData, string $uuid, string $dir): ?array
    {
        $currentUserTmpDirectory = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $uuid, $this->tempDir, $dir]);
        if (!$this->storage->doExiste($currentUserTmpDirectory)) {
            return [];
        }
        $excludeDirs = [$this->paramDir];
        return $this->storage->doInitFile($currentUserTmpDirectory, $this->uriPrefix, $sessionData, $excludeDirs, $this->thumbDir);
    }

    /**
     * Return all the Uploaded Files
     *
     * @param array   $sessionData
     * @param string  $dirPath
     * @param string  $uuid
     *
     * @return array
     */
    public function getDirFiles(?array $sessionData, string $uuid, string $dirPath): array
    {
        $currentDirectory = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $uuid, $dirPath]);
        $this->storage->doCreateDirIfNotExists($currentDirectory);
        $excludeDirs = [$this->paramDir];
        return $this->storage->doInitFiles($currentDirectory, $this->uriPrefix, $sessionData, $excludeDirs, $this->thumbDir);
    }

    /**
     * Return all the Uploaded Files number
     *
     * @param string $uuid
     * @return int
     */
    public function getUploadedFilesNumber(string $uuid): int
    {
        $currentUserTmpDirectory = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $uuid, $this->tempDir]);
        $this->storage->doCreateDirIfNotExists($currentUserTmpDirectory);
        return $this->storage->doGetAllUploadedFilesNumber($currentUserTmpDirectory);
    }

    /**
     * Return the temporary upload directory
     *
     * @return string
     */
    public function getTmpDir(): string
    {
        return $this->tempDir;
    }

    /**
     * Copies a file.
     *
     * If the target file is newer or older than the origin file, it's always overwritten.
     *
     * @param string $originFile
     * @param string $targetFile
     *
     * @return void
     */
    public function copy(string $originFile, string $targetFile): void
    {
        $this->storage->doCopy($originFile, $targetFile);
    }

    /**
     * Get unique path on the root upload Directory.
     *
     * @param string $username
     * @return string
     */
    public function getUserUniqueDir(string $username): string
    {
        return $this->storage->doGetUniqueUserDirectory($this->uploadDirectory, $username);
    }

    /**
     * Checks if the path file exists and is a regular file.
     *
     * @param string       $pitureDir     The cuurent picture directory.
     *
     * @return bool
     */
    public function isFile(?string $pitureDir):bool
    {
        if (!is_string($pitureDir)) {
            return false;
        }

        $info = new \SplFileInfo(substr($this->uploadDirectory, 0, strpos($this->uploadDirectory, $this->uriPrefix)).$pitureDir);

        return $info->isFile() && $info->isReadable() && $info->isWritable();
    }
}
