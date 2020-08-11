<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

use \SplFileInfo;

class FileExtension extends AbstractExtension
{
    /**
     * @var string Upload parameters
     */
    private $uploadDirectory;
    private $uriPrefix;
    private $tmpDirectory;

    /**
     * Constructor
     * @param array $uploadConfigs
     */
    public function __construct(array $uploadConfigs)
    {
        $this->uploadDirectory = $uploadConfigs['uploadDirectory'];
        $this->uriPrefix = $uploadConfigs['uriPrefix'];
        $this->tmpDirectory = $uploadConfigs['tempDir'];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('file_extension', [$this, 'getFileExtension'], ['is_safe' => ['html']]),
            new TwigFilter('file_size', [$this, 'getFileSize'], ['is_safe' => ['html']]),
            new TwigFilter('file_name', [$this, 'getFileName'], ['is_safe' => ['html']]),
            new TwigFilter('file_numbers', [$this, 'getFilesNumber'], ['is_safe' => ['html']]),
            new TwigFilter('directory_size', [$this, 'getDirectorySize'], ['is_safe' => ['html']]),
	        new TwigFilter('tmp_directory_size', [$this, 'getTmpDirectorySize'], ['is_safe' => ['html']])
        ];
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
          new TwigFunction('file_exists', [$this, 'isFile'], ['is_safe' => ['html']])
        );
    }

    public function getFileExtension(?string $picturePath): ?string
    {
        $info = new SplFileInfo($this->doRemoveUriPath($this->uploadDirectory, $this->uriPrefix).$picturePath);
        if ($info->isFile()) {
            return $info->getExtension();
        }
        return null;
    }

    public function isFile(?string $picturePath): bool
    {
        $info = new SplFileInfo($this->doRemoveUriPath($this->uploadDirectory, $this->uriPrefix).$picturePath);
        return $info->isFile();
    }

    public function getFileName(?string $picturePath): ?string
    {
        $info = new SplFileInfo($this->doRemoveUriPath($this->uploadDirectory, $this->uriPrefix).$picturePath);
        if ($info->isFile()) {
            return $info->getBasename();
        }
        return null;
    }

    public function getFileSize(?string $picturePath): ?int
    {
        $info = new SplFileInfo($this->doRemoveUriPath($this->uploadDirectory, $this->uriPrefix).$picturePath);
        if ($info->isFile()) {
            return $info->getSize();
        }
        return null;
    }

    public function getDirectorySize(?string $userDir): ?string
    {
        $info = new SplFileInfo(join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $userDir]));
        if ($info->isDir()){
            return $this->formatBytes($this->folderSize($info->getRealPath()));
        }
        return '0 MB';
    }

    public function getFilesNumber(?string $userDir): ?int
    {
        $info = new SplFileInfo(join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $userDir]));
        if ($info->isDir()){
            return $this->fileCount($info->getRealPath());
        }
        return 0;
    }
    
    public function getTmpDirectorySize(?string $userDir)
    {
	    $info = new SplFileInfo(join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $userDir, $this->tmpDirectory]));
	    if ($info->isDir()){
		    return $this->formatBytes($this->folderSize($info->getRealPath()));
	    }
	    return '0 MB';
    }
	
	private function doRemoveUriPath(string $path, string $uriPrefix): string
    {
        return substr($path, 0, strpos($path, $uriPrefix));
    }

    private function formatBytes($bytes, $precision = 3) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    private function folderSize($dir)
    {
        $size = 0;
        foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->folderSize($each);
        }
        return $size;
    }

    private function fileCount($dir)
    {
        $fi = new \FilesystemIterator($dir, \FilesystemIterator::UNIX_PATHS);
        return iterator_count($fi);
    }
}
