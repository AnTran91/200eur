<?php

namespace App\Handlers\Extractable;


abstract class ArchiveCreator
{
	
	/**
	 * @var ZipArchive
	 */
	protected $archive;
	
	/**
	 * @param $elementArray
	 * @param $dirPath
	 *
	 * @return ZipArchive
	 */
	public function getArchive($elementArray, $dirPath)
	{
		$this->archive = $this->createArchive();
		$this->populateArchive($elementArray, $dirPath);
		
		return $this->archive;
	}
	
	abstract public function createArchive();
	
	abstract public function populateArchive($elementArray, $dirPath);
	
}
