<?php

namespace App\Handlers\Extractable;

use App\Entity\Invoice;
use App\Events\MissingInvoicePdfEvent;
use App\Utils\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class ZipCreator extends ArchiveCreator
{
	
	public function createArchive()
	{
		return new ZipArchive();
	}
	
	/**
	 * @var EventDispatcherInterface
	 */
	private $dispatcher;
	
	public function __construct($dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}
	
	/**
	 * @param Invoice[] $elementArray
	 * @param string $dirPath
	 */
	public function populateArchive($elementArray, $dirPath)
	{
		foreach ($elementArray as $element) {
			// we need to combine them to get the absolute file path
			$filePath = sprintf('%s/%s/%s',
				$dirPath,
				$element->getPaymentDate()->format('Y-m-d'),
				$element->getPdfFileName()
			);
			// test if the file was added
			if (!$this->archive->addElement($filePath, $element->getPdfFileName())) {
				$event = new MissingInvoicePdfEvent($element);
				
				$this->dispatcher->dispatch(Events::ON_MISSING_INVOICE_PDF, $event);
				
				$this->archive->addElement($filePath, $element->getPdfFileName());
			}
		}
	}
}

