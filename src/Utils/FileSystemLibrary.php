<?php
/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Utils;

use Symfony\Component\Filesystem\Filesystem as baseFilesystem;

use Symfony\Component\Filesystem\Exception\IOException;

use App\Exception\FileSystemException;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;
use \ZipArchive;

/**
 * Provides basic utility to manipulate the file system.
 */
class FileSystemLibrary extends baseFilesystem
{
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
    public function move(string $originDir, string $targetDir) :void
    {
        try {
            $this->mirror($originDir, $targetDir);
            $this->chmod($targetDir, 0777, 0000, true);
        } catch (IOException $e) {
            throw new FileSystemException(sprintf("Error copy the images %", $e));
        }
    }

    /**
     * Move a directory to another.
     *
     * @param string $file
     * @param string $targetDir The target directory
     *
     * @return   void
     *
     */
    public function copyFile(string $file, string $targetDir):void
    {
        try {
            $this->copy($file, $targetDir, true);
            $this->chmod($targetDir, 0777, 0000, true);
        } catch (IOException $e) {
            throw new FileSystemException(sprintf("Error copy the images %", $e));
        }
    }

    /**
    * Creates the files directory
    *
    * @param    string  $directory  The directory path
    * @param    int   $mode       The directory mode
    *
    * @return   string The name of the created directory
    *
    * @throws   FileSystemException
    */
    public function createFolder(string $directory, ?int $mode = 0777): ?string
    {
        try {
            if ($this->exists($directory)) {
                return $directory;
            }

            $this->mkdir($directory, $mode);
        } catch (IOException $e) {
            throw new FileSystemException(sprintf("An error occurred while creating your directory at %s", $e->getPath()));
        }

        return $directory;
    }

    /**
    * Deletes files, directories and symlinks
    *
    * @param    mixed  $directory  Element to delelte
    * @return   bool
    *
    * @throws   FileSystemException
    */
    public function delete(string $directory): bool
    {
        try {
            $this->remove($directory);
            return !$this->exists($directory);
        } catch (IOException $e) {
            throw new FileSystemException(sprintf("An error occurred while creating your directory at %s", $e->getPath()));
        }
    }

    /**
     * Creates a temporary directory.
     *
     * @param   string      $prefix     The directory prefix
     * @param   int       $mode       The directory mode
     *
     * @return  string      The name of the created directory
     *
     * @throws FileSystemException  In case operation is failed
     */
    public function createTemporaryDirectory(string $prefix = null, ?int $mode = 0777)
    {
        // get the system tem directory
        $basePath = sys_get_temp_dir();
        // Remove trailing slashes if present
        $basePath = rtrim($basePath, DIRECTORY_SEPARATOR);

        $dir = $basePath.DIRECTORY_SEPARATOR.$prefix.base_convert(mt_rand(0x19A100, 0x39AA3FF), 10, 36);
        try {
            if ($this->exists($dir)) {
                throw new FileSystemException(sprintf("Directory is already exists at %s", $dir));
            }
            $this->mkdir($dir, $mode);

            return $dir;
        } catch (IOException $e) {
            throw new FileSystemException('Unable to create tmp directory');
        }

        throw new FileSystemException('Unreachable code');
    }

    /**
     * Upload the image to the server
     *
     * @param UploadedFile  $file       The file
     * @param string        $directory  The target directory path
     * @param string        $filename   The file name
     *
     * @return   bool
     *
     * @throws  FileSystemException  Throw FileSystemException an exception on failure
     */
    public function moveUploadedFile(UploadedFile $file, string $directory, string $filename = null): bool
    {
        try {
            if (!$this->exists($directory)) {
                $this->createFolder($directory);
            }

            if ($filename === null) {
                $filename = $file->getClientOriginalName();
            }

            $file->move($directory, $filename);

            return $this->exists(join(DIRECTORY_SEPARATOR, [$directory, $filename]));
        } catch (IOException $e) {
            throw new FileSystemException(sprintf("Error copy the images %s", $e->getMessage()));
        } catch (FileException $e) {
            throw new FileSystemException(sprintf("The origin file doesn't Exist %s", $e->getMessage()));
        }
    }

    /**
     * Create a zip file
     *
     * @param string $dirPath  The dir that containes the files
     * @param string $zipPath  The zip path
     * @param string $zipName  The zip name
     * @param array  $exclude  array containes the excluded files
     *
     * @return array
     *
     * @throws AccessDeniedException
     */
    public function createZIP(?string $dirPath, ?string $zipPath, ?string $zipName, ?array $exclude): string
    {
        try {
            $zipDir = new \SplFileInfo(join(DIRECTORY_SEPARATOR, [$zipPath, $zipName]));
            $dirPath = new \SplFileInfo($dirPath);

            $archive = new ZipArchive();

            if (!$this->exists($zipDir->getPathname())) {
                $archive->open($zipDir->getPathname(), ZipArchive::CREATE);
            }else {
                $archive->open($zipDir->getPathname(), ZipArchive::OVERWRITE);
            }

            $finder = new Finder();
            $finder->ignoreUnreadableDirs()->in($dirPath->getPathname())->exclude($exclude);

            foreach ($finder as $elem) {
                if ($elem->isDir()) {
                    $archive->addEmptyDir($elem->getBasename());
                }

                if ($elem->isFile() && $elem->isReadable()) {
                    $archive->addFile($elem->getRealPath(), join(DIRECTORY_SEPARATOR, [basename($elem->getPath()), $elem->getBasename()]));
                }
            }

            $archive->close();
            return $zipDir->getRealPath();
        } catch (\Throwable $e) {
          throw new FileSystemException(sprintf("Error when create file %s", $e->getMessage()));
        }
    }

    /**
     * Get root direcorty contents
     *
     * @param string $dir
     * @param string $exclude
     * @param string $thumbDir
     *
     * @return array
     */
    public function getDirContent(?string $root, ?array $exclude, ?string $thumbDir): array
    {
        $dirFinder = new Finder();
	
	    $dirFinder->sortByAccessedTime();
	    $dirFinder->ignoreUnreadableDirs(true);
        $dirFinder->directories()->ignoreUnreadableDirs()->in($root)->exclude(array_merge($exclude, array($thumbDir)));

        $initialFiles = array();

        foreach ($dirFinder as $directory) {
            if (is_null($initialFile = $this->getFileContent($directory->getRealPath(), $exclude, $thumbDir))) {
                continue;
            }
	
	        array_push($initialFiles, $initialFile);
        }

        return $initialFiles;
    }

    /**
     * Get file contents
     *
     * @param string $dir
     * @param string $exclude
     * @param string $thumbDir
     *
     * @return array
     */
    public function getFileContent(string $dir, ?array $exclude, ?string $thumbDir): ?array
    {
	    $directory = new \SplFileInfo($dir);
		$fileFinder = new Finder();
		
	    $fileFinder->files()->in($directory->getRealPath())->exclude($exclude)->depth('< 1');
	    
	    if (!$fileFinder->hasResults()){
	        return null;
	    }
	    
	    $initialFile = array();
	    foreach ($fileFinder->getIterator() as $file) {
		    $initialFile["thumb_path"] = $file->getRealPath();
		    $initialFile["real_path"] = $file->getRealPath();
		    $initialFile["directory"] = $directory->getBasename();
		    $initialFile["filename"] = $file->getBasename();
		    $initialFile["size"] = $file->getSize();
	    }
	
	    $fileFinder->files()->exclude($exclude)->in(join(DIRECTORY_SEPARATOR, [$directory->getRealPath(), $thumbDir]))->depth('< 1');
	    
	    foreach ($fileFinder->getIterator() as $file) {
		    $initialFile["thumb_path"] = $file->getRealPath();
	    }
	
	    return $initialFile;
    }

    /**
     * Upload chunk
     *
     * @param string        $targetFolder
     * @param int           $index
     * @param UploadedFile  $targetFolder
     *
     * @return bool
     */
    public function addChunk(string $targetFolder, int $index, UploadedFile $chunk): bool
    {
        try {
            // create directory if it does not yet exist
            if (!$this->exists($targetFolder)) {
                $this->createFolder($targetFolder);
            }
            $chunk->move($targetFolder, $index);
            return $this->exists(join(DIRECTORY_SEPARATOR, [$targetFolder, $index]));
        } catch (IOException $e) {
            throw new FileSystemException(sprintf("Error copy the images %s", $e->getMessage()));
        } catch (FileException $e) {
            throw new FileSystemException(sprintf("The origin file doesn't Exist %s", $e->getMessage()));
        }
    }

    /**
     * Assemble the Chunks parts
     *
     * @param Finder  $chunks
     * @param bool    $removeChunk
     *
     * @return UploadedFile
     */
    public function assembleChunks(Finder $chunks, bool $removeChunk): UploadedFile
    {
        $iterator = $chunks->getIterator();
        $base = $iterator->current();
        $iterator->next();
        while ($iterator->valid()) {
            $file = $iterator->current();
            if (false === file_put_contents($base->getPathname(), file_get_contents($file->getPathname()), \FILE_APPEND | \LOCK_EX)) {
                throw new FileSystemException('Reassembling chunks failed.');
            }
            if ($removeChunk) {
                $this->remove($file->getPathname());
            }
            $iterator->next();
        }
        $name = $base->getBasename();
        $assembled = new File($base->getRealPath());
        $assembled = $assembled->move($base->getPath(), $name);

        return new UploadedFile($assembled->getPathname(), $assembled->getBasename(), null, $assembled->getSize(), null, true);
        ;
    }

    /**
     * Get chunks parts
     *
     * @param string $directory
     *
     * @return Finder
     */
    public function getChunks(string $directory) : Finder
    {
        $finder = new Finder();
        $finder
            ->in($directory)->files()->sort(function (\SplFileInfo $a, \SplFileInfo $b) {
                $t = explode('_', $a->getBasename());
                $s = explode('_', $b->getBasename());
                $t = (int) $t[0];
                $s = (int) $s[0];
                return $s < $t;
            });
        return $finder;
    }

    /**
     * Deletes all file parts in the chunks folder for files uploaded
     *
     * @param string $chunksFolder
     * @param string $chunksExpireIn
     *
     * @return void
     */
    public function cleanupChunks(string $chunksFolder): void
    {
        if (!$this->exists($chunksFolder)) {
            return;
        }

        $fileFinder = new Finder();

        $fileFinder->files()->in($chunksFolder);

        foreach ($fileFinder as $file) {
            if ($file->isFile() && $file->isReadable() && $file->isWritable() && !$file->isLink()) {
                $this->delete($file->getRealPath());
            }
        }
        $this->delete($chunksFolder);
    }

    /**
     * Returns a path to use with this upload. Check that the name does not exist,
     * and appends a suffix otherwise.
     *
     * @param string $uploadDirectory   Target directory
     * @param string $uuid              The name of the file to use.
     *
     * @return string
     */
    public function getUniqueTargetPath(string $uploadDirectory, string $uuid = null): string
    {
        if (is_null($uuid)) {
            $uuid = UUID::v4();
        }

        $unique = $uuid;
        // Get unique file name for the file, by appending random suffix.
        while ($this->exists(join(DIRECTORY_SEPARATOR, [$uploadDirectory, $unique]))) {
            $unique = UUID::v4();
        }
        return $unique;
    }

    /**
     * Generate unique user directory
     *
     * @param string $uploadDirectory
     * @param string $username
     *
     * @return string
     */
    public function getUniqueUserDirectory(string $uploadDirectory, string $username): string
    {
        $unique = UUID::v4User($username);
        // Get unique file name for the file, by appending random suffix.
        while ($this->exists(join(DIRECTORY_SEPARATOR, [$uploadDirectory, $unique]))) {
            $unique = UUID::v4User($username);
        }
        return $unique;
    }
}
