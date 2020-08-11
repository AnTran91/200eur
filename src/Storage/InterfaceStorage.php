<?php
/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Storage;

use App\Exception\FileSystemException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Abstract Storage.
 */
interface InterfaceStorage
{
    /**
     * Do real upload.
     *
     * @param UploadedFile    $file           The file
     * @param string          $dir            The directory in which the file is uploaded
     * @param string          $name           The file name
     *
     * @return bool
     *
     * @throws   FileSystemException  Throw FileSystemException an exception on failure
     */
    public function doUpload(UploadedFile $file, string $dir, string $name = null): bool;

    /**
     * Move a directory to another.
     *
     * @param string	$originDir	The origin directory
     * @param string	$targetDir	The target directory
     *
     * @return   void
     *
     * @throws   FileSystemException  Throw FileSystemException an exception on failure
     */
    public function doMoveFiles(string $originDir, string $targetDir) :void;

    /**
     * Do chunked upload.
     *
     * @param string          $targetFolder   The directory path
     * @param string          $index          The blob file index
     * @param UploadedFile    $chunk          The blob file
     *
     * @return bool
     *
     * @throws   FileSystemException  Throw FileSystemException an exception on failure
     */
    public function doChunkedUpload(string $targetFolder, string $index, UploadedFile $chunk): bool;

    /**
     * Do remove.
     *
     * @param string  $dir  The directory path
     *
     * @return bool
     *
     * @throws   FileSystemException  Throw FileSystemException an exception on failure
     */
    public function doRemove(string $dir): bool;

    /**
     * Do existe.
     *
     * @param string  $dir  The directory path
     *
     * @return bool
     *
     * @throws   FileSystemException  Throw FileSystemException an exception on failure
     */
    public function doExiste(string $dir): bool;

    /**
     * Do Create Dir.
     *
     * @param string  $dir  The directory path
     *
     * @return void
     *
     * @throws   FileSystemException  Throw FileSystemException an exception on failure
     */
    public function doCreateDirIfNotExists(string $dir): void;

    /**
     * Do combine chunks.
     *
     * @param string  $chunksFolder The directory path
     *
     * @return UploadedFile
     *
     * @throws   FileSystemException  Throw FileSystemException an exception on failure
     */
    public function doCombineChunks(string $chunksFolder): UploadedFile;

    /**
     * Do clean up chunks.
     *
     * @param string  $targetFolder The directory path
     *
     * @return void
     *
     * @throws   FileSystemException  Throw FileSystemException an exception on failure
     */
    public function doCleanupChunks(string $targetFolder): void;

    /**
     * Copies a file.
     *
     * If the target file is newer or older than the origin file, it's always overwritten.
     *
     * @param string $originFile          The original filename
     * @param string $targetFile          The target filename
     *
     * @throws FileNotFoundException When originFile doesn't exist
     * @throws IOException           When copy fails
     */
    public function doCopy(string $originFile, string $targetFile): void;

    /**
     * Do resolve path.
     *
     * @param string        $dir           The directory in which the files is uploaded
     * @param string        $uriPrefix     The session data
     * @param array|null    $sessionData
     * @param array         $exclude        Excludes directories.
     * @param string        $thumbDir
     *
     * @return array
     *
     */
    public function doInitFiles(string $dir, string $uriPrefix, ?array $sessionData, ?array $exclude, ?string $thumbDir): ?array;

    /**
     * Do resolve path.
     *
     * @param string        $dir           The directory in which the files is uploaded
     * @param string        $uriPrefix     The session data
     * @param array|null    $sessionData
     * @param array         $exclude        Excludes directories.
     * @param string        $thumbDir
     *
     * @return array
     *
     */
    public function doInitFile(string $dir, string $uriPrefix, ?array $sessionData, ?array $exclude, ?string $thumbDir): ?array;

    /**
     * Returns a path to use with this upload. Check that the uuid does not exist,
     * and appends a suffix otherwise.
     *
     * @param string $uploadDirectory Target directory
     * @param string $uuid            The uuid.
     *
     * @return string
     *
     * @throws   FileSystemException  Throw FileSystemException an exception on failure
     */
    public function doGetUniqueTargetPath(string $uploadDirectory, string $uuid = null): string;

    /**
     * Returns the uri Path.
     *
     * @param string $path        The file Path
     * @param string $uriPrefix   The uri prefix
     *
     * @return string
     *
     * @throws   FileSystemException  Throw FileSystemException an exception on failure
     */
    public function doGetUriPath(string $path, string $uriPrefix): string;

    /**
     * Generate unique user directory
     *
     * @param string $uploadDirectory
     * @param string $username
     *
     * @return string
     */
    public function doGetUniqueUserDirectory(string $uploadDirectory, string $username): string;
}
