<?php

namespace App\Controller\Admin;


use App\Controller\Admin\Traits\ControllerTrait;
use App\Entity\Invoice;
use App\Events\MissingInvoicePdfEvent;
use App\Utils\Events;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_INVOICE_MANAGER') or is_granted('ROLE_ORDER_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class PDFController extends Controller
{
    use ControllerTrait;
	
	/**
	 * Downloads invoice Action
	 *
	 * @Route("/download/{id}", name="admin_invoice_download", requirements={"id"="\d+"}, methods="GET")
	 *
	 * @param Invoice $invoice
	 * @return BinaryFileResponse
	 */
    public function downloadPDF(Invoice $invoice): BinaryFileResponse
    {
        $targetPDF = join(DIRECTORY_SEPARATOR, [$this->getParameter('fpdf_config')['media'], $invoice->getPaymentDate()->format('Y-m-d'), $invoice->getPdfFileName()]);

        $info = new \SplFileInfo($targetPDF);
        if (!$info->isFile()) {
            $event = new MissingInvoicePdfEvent($invoice);
            $this->dispatch(Events::ON_MISSING_INVOICE_PDF, $event);
        }

        return $this->file($info->getRealPath(), $invoice->getPdfFileName(), ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }
}