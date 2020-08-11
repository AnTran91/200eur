<?php

namespace App\Controller\Admin;

use App\Entity\Invoice;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Controller\Admin\Traits\ControllerTrait;
use App\Utils\Events;
use App\Events\MissingInvoicePdfEvent;
use App\Handlers\PdfGenerator;
use App\Handlers\PDFHandlers\AdditionalInvoice;
use App\Handlers\PDFHandlers\OrdinaryInvoice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/invoice")
 */
class InvoiceActionController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/renumber", name="invoice_renumber", methods="GET")
     * 
     * @param Request $request
     */
    public function renumber(Request $request)
    {
        $fromDate = $request->query->get('from');
        // $toDate = $request->query->get('to');

        $entityManager = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getManager()->getRepository(Invoice::class);

        $from = new \Datetime($fromDate);
        $to = new \Datetime('now');
        $invoices = $repo->findByDate($from, $to);

        $index = null;

        for ($i = 0; $i <= count($invoices) - 1; $i++){
            if ($invoices[$i]->getType() === Invoice::WALLET){
                $invoices[$i]->setInvoiceNumber(null);
                $invoices[$i]->setPdfFileName(sprintf('COMMANDE-TIRELIRE-%06d-%s.pdf', $invoices[$i]->getCurrentOrder()->getOrderNumber(), (new \DateTime('now'))->format('d-m-Y')));
                $entityManager->flush();
            } else {
                if (is_null($index)){
                    $index = $invoices[$i]->getInvoiceNumber();
                } else {
                    $index++;
                    $invoices[$i]->setInvoiceNumber(strval($index));
                    $invoices[$i]->setPdfFileName(sprintf('%06d-%s.pdf', $index, $invoices[$i]->getPaymentDate()->format('d-m-Y')));
                    $entityManager->flush();
                }
            }
        };
        // $entityManager->flush();exit;
        // dump($invoices[0]);exit;
        // echo 'ok';exit;

        return $this->redirectToRoute('admin_invoice_index');
    }

    // public function regenerateInvoicePdf(Invoice $invoice){
    //     $pdf = new \App\FPDF\PdfInvoice();
    //     $translator = new TranslatorInterface();
    //     $pdf->setTranslator($translator);
    //     $pdf->AddPage();
    //     $pdf->setHeader($logoPath);
    //     $pdf->setRechargeInvoiceTitle($invoice, $invoice->getPaymentDate()->format('d-m-Y'));
    //     $pdf->setClient($invoice->getClient());
    //     $pdf->setOrder($invoice->getCurrentOrder());
    //     $populateInvoiceTable($invoice);

    //     $targetDir = join(DIRECTORY_SEPARATOR, [$media, $invoice->getPaymentDate()->format('Y-m-d')]);
    //     $storage->doCreateDirIfNotExists($targetDir);
    //     $pdf->Close();
    //     $pdf->Output(join(DIRECTORY_SEPARATOR, [$targetDir, $invoice->getPdfFileName() ]), 'F');
    // }
}