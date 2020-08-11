<?php

namespace App\Handlers\Extractable;


use App\Entity\User;
use App\Entity\Wallet;

class ExcelLastLoginV2 extends ArchiveCreator
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
		$this->archive->addElement('D1', 'ORDERS NUMBER');
		$this->archive->addElement('E1', 'STATUS');
		$this->archive->addElement('F1', 'DATE');
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
			$numbers = '';
			foreach ($element->getOrders()->toArray() as $order) {
				if ($order->getOrderNumber() != null) {
					$numbers .= $order->getOrderNumber() . ', ';
				}
			}
			if ($numbers != '') {
				$this->archive->addElement('A' . $cellNumber, $element->getFirstName());
				$this->archive->addElement('B' . $cellNumber, $element->getLastName());
				$this->archive->addElement('C' . $cellNumber, $element->getEmail());
				$this->archive->addElement('D' . $cellNumber, $numbers);
				if ($element->isEnabled()) {
					$this->archive->addElement('E' . $cellNumber, 'ACTIVE');
				} else {
					$this->archive->addElement('E' . $cellNumber, 'NON ACTIVE');
				}
				$this->archive->addElement('F' . $cellNumber, date('Y-m-d H:i:s'));
				$cellNumber++;
			}
		}
	}
}
