<?php

namespace App\EventSubscriber;

use App\Exception\FileSystemException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use App\Events\OrderEvent;
use App\Events\MissingInvoicePdfEvent;

use App\Utils\Events;
use App\Handlers\PdfGenerator;

use App\Entity\Invoice;

/**
 * @see \App\Events\OrderEvent class.
 */
final class PDFGeneratorEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var PdfGenerator
     */
    private $pdfGenerator;

    /**
     * Constructor
     *
     * @param \App\Handlers\PdfGenerator            $pdfGenerator
     */
    public function __construct(PdfGenerator $pdfGenerator)
    {
        $this->pdfGenerator = $pdfGenerator;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
          Events::ON_FREE_ORDER => array(
            array('OnGenerateInvoicePDF', -10),
            array('OnGenerateOrganisationMonthlyInvoicePDF', -20)
          ),
          Events::ON_PAY_ORDER_BY_WALLET => array(
            array('OnGenerateWalletInvoicePDF', -10),
            array('OnGenerateOrganisationMonthlyInvoicePDF', -20)
          ),
          Events::ON_PAY_ORDER_BY_TRANSACTION => array(
            array('OnGenerateInvoicePDF', -10),
            array('OnGenerateOrganisationMonthlyInvoicePDF', -20)
          ),
          Events::ON_UPDATE_INVOICE => array(
            array('OnGenerateInvoicePDF', -10),
            array('OnGenerateRechargeInvoicePDF', -20),
            array('OnGenerateWalletInvoicePDF', -30),
//	        array('onGenerateAdditionalInvoicePDF', -40),
            array('OnGenerateUserMonthlyInvoicePDF', -50),
            array('OnGenerateOrganisationMonthlyInvoicePDF', -60)
          ),
          Events::ON_ADD_ADDITIONAL_INVOICE => array(
            array('onGenerateAdditionalInvoicePDF', -10)
          ),
          Events::ON_SAVE_MONTHLY_ORDER => array(
            array('OnGenerateUserMonthlyInvoicePDF', -10),
            array('OnGenerateOrganisationMonthlyInvoicePDF', -20)
          ),
          Events::ON_CREATE_IMMOSQUARE_ORDER => array(
            array('OnGenerateUserMonthlyInvoicePDF', -10)
          ),
          Events::ON_MISSING_INVOICE_PDF => array(
            array('OnGenerateMissingInvoicePdf', -10),
          ),
          Events::ON_RECHARGE_TO_WALLET => array(
            array('OnGenerateRechargeInvoicePDF', -10)
          )
        ];
    }

    /**
     * This method client invoice then saved to the system file
     *
     * @param OrderEvent $orderEvent
     *
     * @return void
     */
    public function OnGenerateRechargeInvoicePDF(OrderEvent $orderEvent)
    {
        $invoice = $orderEvent->getOrder()->findOneInvoiceByType([Invoice::RECHARGE]);
        if (is_null($invoice)) {
            return;
        }

        try {
            $this->pdfGenerator->setInvoice($invoice);
            $this->pdfGenerator->createInvoicePdf();
        } catch (FileSystemException $e) {
            $orderEvent->setErrorType(OrderEvent::TYPE_FILE_SYSTEM_ERROR);
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }

    /**
     * This method client invoice then saved to the system file
     *
     * @param OrderEvent $orderEvent
     *
     * @return void
     */
    public function OnGenerateInvoicePDF(OrderEvent $orderEvent)
    {
        $invoice = $orderEvent->getOrder()->findOneInvoiceByType([Invoice::ORDINARY]);
        if (is_null($invoice)) {
            return;
        }

        try {
            $this->pdfGenerator->setInvoice($invoice);
            $this->pdfGenerator->createInvoicePdf();
        } catch (FileSystemException $e) {
            $orderEvent->setErrorType(OrderEvent::TYPE_FILE_SYSTEM_ERROR);
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }

    /**
     * This method client invoice then saved to the system file
     *
     * @param OrderEvent $orderEvent
     *
     * @return void
     */
    public function OnGenerateWalletInvoicePDF(OrderEvent $orderEvent)
    {
        $invoice = $orderEvent->getOrder()->findOneInvoiceByType([Invoice::WALLET]);
        if (is_null($invoice)) {
            return;
        }

        try {
            $this->pdfGenerator->setInvoice($invoice);
            $this->pdfGenerator->createInvoicePdf();
        } catch (FileSystemException $e) {
            $orderEvent->setErrorType(OrderEvent::TYPE_FILE_SYSTEM_ERROR);
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }

    /**
     * This method additional invoice then saved to the system file
     *
     * @param OrderEvent $orderEvent
     *
     * @return void
     */
    public function onGenerateAdditionalInvoicePDF(OrderEvent $orderEvent): void
    {
        $additionalInvoice = $orderEvent->getOrder()->findOneInvoiceByType([Invoice::ADDITIONAL]);
        if (is_null($additionalInvoice)) {
            return;
        }

        try {
            $this->pdfGenerator->setInvoice($additionalInvoice);
            $this->pdfGenerator->createInvoicePdf();
        } catch (FileSystemException $e) {
            $orderEvent->setErrorType(OrderEvent::TYPE_FILE_SYSTEM_ERROR);
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }

    /**
     * This method generate monthly invoice then saved to the system file
     *
     * @param OrderEvent $orderEvent
     *
     * @return void
     */
    public function OnGenerateUserMonthlyInvoicePDF(OrderEvent $orderEvent): void
    {
        $invoice = $orderEvent->getOrder()->findOneInvoiceByType([Invoice::MONTHLY_PER_USER]);
        if (is_null($invoice)) {
            return;
        }

        try {
            $this->pdfGenerator->setInvoice($invoice);
            $this->pdfGenerator->createInvoicePdf();
        } catch (FileSystemException $e) {
            $orderEvent->setErrorType(OrderEvent::TYPE_FILE_SYSTEM_ERROR);
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }

    /**
     * @param OrderEvent $orderEvent
     */
    public function OnGenerateOrganisationMonthlyInvoicePDF(OrderEvent $orderEvent): void
    {
        $invoice = $orderEvent->getOrder()->findOneInvoiceByType([Invoice::MONTHLY_PER_ORGANIZATION]);
        if (is_null($invoice)) {
            return;
        }

        try {
            $this->pdfGenerator->setInvoice($invoice);
            $this->pdfGenerator->createInvoicePdf();
        } catch (FileSystemException $e) {
            $orderEvent->setErrorType(OrderEvent::TYPE_FILE_SYSTEM_ERROR);
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }

    /**
     * @param MissingInvoicePdfEvent $missingEvent
     */
    public function OnGenerateMissingInvoicePdf(MissingInvoicePdfEvent $missingEvent): void
    {
      try {
          $invoice = $missingEvent->getInvoice();
          $this->pdfGenerator->setInvoice($invoice);
          $this->pdfGenerator->createInvoicePdf();
      } catch (FileSystemException $e) {
          $missingEvent->stopPropagation();
      }
    }
}
