<?php

namespace App\Handlers\Extractable;

use Symfony\Component\Filesystem\Filesystem;


class ZipArchive extends Archive
{
	
	/**
	 * @var Filesystem
	 */
	private $filesystem;
	
	/**
	 * @var string
	 */
	private $publicDirPath;
	
	public function __construct()
	{
		parent::__construct();
		$this->filesystem = new Filesystem();
	}
	
	public function createArchive()
	{
		$this->archive = new \ZipArchive();
		$this->archive->open('files.zip', \ZipArchive::OVERWRITE | \ZipArchive::CREATE);
	}
	
	public function addElement($filePath, $fileName)
	{
		if ($this->filesystem->exists($filePath)) {
			$this->archive->addFile($filePath, $fileName);
			return True;
		}
		return False;
	}
	
	public function save()
	{
		$this->archive->close();
	}
	
	public function setPublicDirPath($publicDirPath)
	{
		$this->publicDirPath = $publicDirPath;
	}
	
	public function getArchivePath()
	{
		return $this->publicDirPath . 'files.zip';
	}
	
}

