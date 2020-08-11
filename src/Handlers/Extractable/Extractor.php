<?php

namespace App\Handlers\Extractable;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Extractor
{
	/**
	 * @var EventDispatcherInterface
	 */
	private $dispatcher;
	
	public function __construct(EventDispatcherInterface $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}
	
	/**
	 * @param string $type
	 * @param array  $elementArray
	 * @param string $dirPath
	 * @param string $publicDirPath
	 *
	 * @return ZipArchive|null
	 */
	public function createArchive($type, $elementArray, $dirPath, $publicDirPath)
	{
		if ($type == 'zip') {
			$zipCreator = new ZipCreator($this->dispatcher);
			$zip = $zipCreator->getArchive($elementArray, $dirPath);
			$zip->setPublicDirPath($publicDirPath);
			return $zip;
		}
		
		if ($type == 'excel') {
			$excelCreator = new ExcelCreator();
			$excel = $excelCreator->getArchive($elementArray, $dirPath);
			$excel->setPublicDirPath($publicDirPath);
			$excel->save();
			return $excel;
		}

		if ($type == 'excel_wallet') {
			$excelCreator = new ExcelWalletAmountCreator();
			$excel = $excelCreator->getArchive($elementArray, $dirPath);
			$excel->setPublicDirPath($publicDirPath);
			$excel->save();
			return $excel;
		}

		if ($type == 'excel_last_login') {
			$excelCreator = new ExcelLastLoginV2();
			$excel = $excelCreator->getArchive($elementArray, $dirPath);
			$excel->setPublicDirPath($publicDirPath);
			$excel->save();
			return $excel;
		}
		
		return null;
	}
	
}
