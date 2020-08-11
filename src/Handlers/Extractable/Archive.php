<?php

namespace App\Handlers\Extractable;

abstract class Archive
{
	
	/**
	 * @var \ZipArchive
	 */
	protected $archive;
	
	/**
	 * @var string
	 */
	protected $archivePath;
	
	public function __construct()
	{
		$this->createArchive();
	}
	
	abstract public function createArchive();
	
	abstract public function addElement($param1, $param2);
	
	abstract public function save();
	
	abstract public function getArchivePath();
}
