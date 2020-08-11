<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Invoice;
use App\Entity\Order;

use App\Form\Admin\Filters\InvoiceFilterType;

/**
 * @Route("invoice")
 * @Security("is_granted('ROLE_INVOICE_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class InvoiceController extends Controller
{
    use ControllerTrait;
	
	/**
	 * @Route("/", name="admin_invoice_index", methods="GET|POST")
	 *
	 * @param Request $request
	 * @return Response
	 * @throws \Exception
	 */
    public function index(Request $request): Response
    {
        $this->getFilters()->disable('soft_deleteable');

        $invoices = $this->dynamicPaginator([
          'filter' => $request->query->get('filter', []),
          'page' => $request->query->get('page', 1)
        ], Invoice::class, ['currentOrders', 'organization']);

        if ($request->isXmlHttpRequest()) {
            return $this->json([
            'html' => $this->renderView('admin/invoice/_list.html.twig', ['invoices' => $invoices]),
            'page' => $invoices->getPaginationData()['current'],
            'params' => $invoices->getParams(),
            'total_page' => $invoices->getPaginationData()['pageCount']
          ]);
        }

        return $this->render('admin/invoice/index.html.twig', [
        'invoices' => $invoices,
        'filter_form' => $this->createForm(InvoiceFilterType::class)->createView()
      ]);
    }
	
	/**
	 * @Route("/{id}", name="admin_invoice_show", methods="GET")
	 *
	 * @param Request $request
	 * @param Invoice $invoice
	 * @return Response
	 */
    public function show(Request $request, Invoice $invoice): Response
    {
        $orders = $this->basicPaginator([
          'page' => $request->query->get('page', 1)
        ], $this->getDoctrine()->getManager()->getRepository(Order::class)->getQueryFindByInvoice($invoice->getId()));

        return $this->render('admin/invoice/show.html.twig', ['invoice' => $invoice, 'orders' => $orders]);
    }
}
