<?php

namespace App\Handlers\Extractable;


use App\Entity\Invoice;

class ExcelCreator extends ArchiveCreator
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
		$this->archive->addElement('A1', 'NUMERO');
		$this->archive->addElement('B1', 'MONTANT TOTAL(HORS TVA)');
		$this->archive->addElement('C1', 'TVA');
		$this->archive->addElement('D1', 'MONTANT TOTAL AVEC TVA');
		$this->archive->addElement('E1', 'MONTANT REDUCTION TOTAL');
		$this->archive->addElement('F1', 'NOMBRE D\'IMAGES AVEC REDUCTION');
		$this->archive->addElement('G1', 'CLIENT');
		$this->archive->addElement('H1', 'ADRESSE EMAIL');
		$this->archive->addElement('I1', 'DATE CREATION');
	}
	
	/**
	 * @param Invoice[] $elementArray
	 * @param string $dirPath
	 */
	public function populateArchive($elementArray, $dirPath)
	{
		$this->setHeader();
		
		$cellNumber = 2;
		
		foreach ($elementArray as $element) {
			$this->archive->addElement('A' . $cellNumber, $element->getInvoiceNumber());
			$this->archive->addElement('B' . $cellNumber, $element->getTotalAmount() - $element->getTotalReductionAmount());
			$this->archive->addElement('C' . $cellNumber, $element->getTaxPercentage());
			$this->archive->addElement('D' . $cellNumber, $element->getTotalAmountPaid());
			$this->archive->addElement('E' . $cellNumber, $element->getTotalReductionAmount());
			$this->archive->addElement('F' . $cellNumber, $element->getTotalReductionOnPictures());
			$this->archive->addElement('G' . $cellNumber, $element->getClient()->getFullName());
			$this->archive->addElement('H' . $cellNumber, $element->getClient());
			$this->archive->addElement('I' . $cellNumber, $element->getCreationDate());
			$cellNumber++;
		}
	}
	
}

