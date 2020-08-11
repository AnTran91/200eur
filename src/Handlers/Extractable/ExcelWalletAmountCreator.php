<?php

namespace App\Handlers\Extractable;


use App\Entity\User;
use App\Entity\Wallet;

class ExcelWalletAmountCreator extends ArchiveCreator
{
	/**
	 * @return ExcelArchive
	 */
	public function createArchive()
	{
		return new ExcelArchive();
	}
	
	public function setHeader()
	{
		$this->archive->addElement('A1', 'FIRST NAME');
		$this->archive->addElement('B1', 'LAST NAME');
		$this->archive->addElement('C1', 'EMAIL');
		$this->archive->addElement('D1', 'WALLET AMOUNT');
		$this->archive->addElement('E1', 'DATE');
	}
	
	/**
	 * @param User[] $elementArray
	 * @param string $dirPath
	 */
	public function populateArchive($elementArray, $dirPath)
	{
		$this->setHeader();
		
		$cellNumber = 2;
		
		foreach ($elementArray as $element) {
			$this->archive->addElement('A' . $cellNumber, $element->getFirstName());
			$this->archive->addElement('B' . $cellNumber, $element->getLastName());
			$this->archive->addElement('C' . $cellNumber, $element->getEmail());
			$this->archive->addElement('D' . $cellNumber, $element->getWallet()->getCurrentAmount());
			$this->archive->addElement('E' . $cellNumber, date('Y-m-d H:i:s'));
			$cellNumber++;
		}
	}
	
}
