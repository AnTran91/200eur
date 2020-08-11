<?php

namespace App\Handlers\Extractable;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class ExcelArchive extends Archive
{
	
	/**
	 * @var Spreadsheet
	 */
	private $spreadSheet;
	
	/**
	 * @var string
	 */
	private $publicDirPath;
	
	/**
	 * @var Worksheet
	 */
	private $activeSheet;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 */
	public function createArchive()
	{
		$this->spreadSheet = new Spreadsheet();
		$this->activeSheet = $this->spreadSheet->getActiveSheet();
		foreach (range('A', 'I') as $col) {
			$this->activeSheet->getColumnDimension($col)->setAutoSize(true);
		}
	}
	
	public function addElement($position, $data)
	{
		$this->activeSheet->setCellValue($position, $data);
	}
	
	/**
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	public function save()
	{
		$writer = new Xlsx($this->spreadSheet);
		$writer->save('file.xlsx');
	}
	
	public function setPublicDirPath($publicDirPath)
	{
		$this->publicDirPath = $publicDirPath;
	}
	
	public function getArchivePath()
	{
		return $this->publicDirPath . 'file.xlsx';
	}
	
}
