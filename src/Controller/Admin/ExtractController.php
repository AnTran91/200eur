<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Invoice;
use App\Entity\User;

use App\Handlers\Extractable\Extractor;

use App\Form\Admin\ExtractInvoiceType;

/**
 * @Route("/extract")
 * @Security("is_granted('ROLE_SUPER_ADMIN')")
 */
class ExtractController extends Controller
{
    use ControllerTrait;

    /**
     * @var array
     */
    private $countryTaxList;

    /**
     * @var string
     */
    private $invoiceDirPath;

    /**
     * InvoiceController constructor.
     *
     * @param array $fpdfConfigs
     * @param array $countryTaxList
     */
    public function __construct(array $fpdfConfigs, array $countryTaxList)
    {
        $this->invoiceDirPath = $fpdfConfigs['media'];
        $this->countryTaxList = $countryTaxList;
    }

    /**
     * @Route("/", name="extract_index", methods="GET|POST")
     *
     * @param Request $request
     * @param Extractor $extractor
     * @return Response
     */
    public function index(Request $request, Extractor $extractor): Response
    {
        $form = $this->createForm(ExtractInvoiceType::class);
        $form->handleRequest($request);

        $publicDirPath = $this->get('kernel')->getProjectDir() . '/public/';

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            //preg_match("/^\d{2}\/\d{2}\/\d{4} - \d{2}\/\d{2}\/\d{4}$/", $data['date']);
            list($start, $end) = explode(' - ', $data['date']);
            $startDate = new \DateTime($start);
            $endDate = new \DateTime($end);

            // filtering the invoices by user, date interval and state
            $invoices = $this->getDoctrine()->getRepository(Invoice::class)->findByUserAndDate($data['user'], $startDate, $endDate, $data['paymentType']);

            // if there is no invoice found redirect with 'nothing found!' message
            if (empty($invoices)) {
                $this->addFlash('no_invoice_found', $this->trans('admin.invoice.not_found', [], 'admin'));
                return $this->redirectToRoute('extract_index');
            }

            $fileTail = '';
            // If the wanted archive is zip
            if ($form->getClickedButton()->getName() === 'zip') {
                $file = $extractor->createArchive('zip', $invoices, $this->invoiceDirPath, $publicDirPath);
                $fileTail .= '.zip';
            }
            // If the wanted archive is xlsx
            if ($form->getClickedButton()->getName() === 'excel') {
                $file = $extractor->createArchive("excel", $invoices, $this->invoiceDirPath, $publicDirPath);
                $fileTail .= '.xlsx';
            }

            return $this->file($file->getArchivePath(), !is_null($data['paymentType']) ? $this->trans('file_name.' . $data['paymentType'], [], 'admin') . $fileTail : 'Toutes les factures' . $fileTail);
        }

        return $this->render('admin/extract/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/current-wallet/", name="generate_current_wallet_amount")
     * 
     * @param Request $request
     * @param Extractor $extractor
     * @return Response
     */
    public function generateCurrentWalletAmount(Extractor $extractor)
    {
        $publicDirPath = $this->get('kernel')->getProjectDir() . '/public/';

        $users = $this->getDoctrine()->getManager()->getRepository(User::class)->findAll();
        $file = $extractor->createArchive("excel_wallet", $users, $this->invoiceDirPath, $publicDirPath);
        return $this->file($file->getArchivePath(), 'Solde TIRELIRE ' . date('d-m-Y') . '.xlsx');
    }

    /**
     * @Route("/last-login-v2/", name="generate_last_login_since_v2")
     * 
     * @param Request $request
     * @param Extractor $extractor
     * @return Response
     */
    public function generateLastLoginSinceV2(Extractor $extractor)
    {
        $publicDirPath = $this->get('kernel')->getProjectDir() . '/public/';

        $users = $this->getDoctrine()->getManager()->getRepository(User::class)->findAll();
        $file = $extractor->createArchive("excel_last_login", $users, $this->invoiceDirPath, $publicDirPath);
        return $this->file($file->getArchivePath(), 'order_once' . date('d-m-Y') . '.xlsx');
    }
}
