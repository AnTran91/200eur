<?php

namespace App\Controller\Emmobilier;

use App\Controller\Emmobilier\Traits\ControllerTrait;
use App\Events\MissingInvoicePdfEvent;
use App\Utils\Events;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

use App\Handlers\OrderArchiveHandler;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Knp\Component\Pager\PaginatorInterface;
use App\Handlers\OrderHandler;

use App\Repository\OrderRepository;

use App\Entity\Order;
use App\Entity\Invoice;

use App\Form\Emmobilier\OrderFilterType;

/**
 * @Route("/orders")
 * @Security("is_granted('ROLE_EMMOBILIER_USER')")
 */
class OrderController extends Controller
{
    use ControllerTrait;
	
	/**
	 * List Action
	 *
	 * @Route("/", name="order_list", methods="GET")
	 *
	 * @param Request $request
	 * @param OrderRepository $repository
	 * @param PaginatorInterface $paginator
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function list(Request $request, OrderRepository $repository,  PaginatorInterface $paginator)
    {
    	/** @var \Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter $filter */
        $filter = $this->getFilters()->enable('soft_deleteable');
        $filter->enableForEntity(Order::class);

        $form = $this->createForm(OrderFilterType::class, null, array('method' => 'GET'))->handleRequest($request);

        if ($form->isSubmitted()) {
           	$query = $repository->findAllOrderByUserAndStatus($this->getUser(), $request->get('status', $form->get('status')->getData()));
        }else {
            $query = $repository->findAllOrderByUserAndStatus($this->getUser(), null);
        }

        $orders = $paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('limit_order_per_page'));
        $orderCount = $repository->countOrdersByUser($this->getUser()->getId());

        return $this->render('emmobilier/order/index.html.twig', [
          'form' => $form->createView(),
          'orders' => $orders,
          'orderCount' => $orderCount['orderCount'],
        ]);
    }
	
	/**
	 * Show Action
	 *
	 * @Route("/details/{id}", name="order_show", requirements={"id"="\d+"}, methods="GET")
	 * @Entity("order", expr="repository.findOrderByID(id)")
	 * @Cache(lastModified="order.getUpdatedAt()", ETag="'OrderCreation' ~ order.getId() ~ order.getUpdatedAt().getTimestamp()")
	 *
	 * @param Order $order
	 * @param OrderHandler $orderHandler
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function show(Order $order, OrderHandler $orderHandler)
    {
        return $this->render('emmobilier/order/show.html.twig', [
          'order' => $order,
          'response' => $orderHandler->getFormattedOrder($order),
        ]);
    }
	
	/**
	 * Verify Action
	 *
	 * @Route("/verify/{id}", name="order_verify", requirements={"id"="\d+"}, methods="GET")
	 * @Entity("order", expr="repository.findOrderByID(id)")
	 * @Cache(lastModified="order.getUpdatedAt()", ETag="'OrderCreation' ~ order.getId() ~ order.getUpdatedAt().getTimestamp()")
	 *
	 * @param Order $order
	 * @param OrderHandler $orderHandler
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function verify(Order $order, OrderHandler $orderHandler)
    {
    	if (in_array($order->getOrderStatus(), [Order::DELIVERY_SHORT_TIME_READY, Order::PARTIALLY_COMPLETED, Order::SEND_TO_CLIENT])){
    	    $this->createAccessDeniedException('order status is not valid');
	    }
	    
        return $this->render('emmobilier/order/verify.html.twig', [
          'order' => $order,
          'response' => $orderHandler->getFormattedOrder($order),
        ]);
    }
	
	/**
	 * Soft delete
	 *
	 * @Route("/delete/{id}", requirements={"id"="\d+"}, name="order_delete", methods="DELETE")
	 *
	 * @param Request $request
	 * @param Order $order
	 * @param TranslatorInterface $translator
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
    public function delete(Request $request, Order $order, TranslatorInterface $translator)
    {
        try{
	        if ($this->isCsrfTokenValid('delete-order-'.$order->getId(), $request->request->get('_token'))) {
		        $em = $this->getDoctrine()->getManager();
		        $em->remove($order);
		        $em->flush();
	        }
	
	        $response = ['success' => true, 'msg' => $translator->trans('orders.msg.delete')];
        }catch (\Exception $e){
	        $response = ['success' => false, 'msg' => $e->getMessage()];
        }

        return $this->json($response);
    }
	
	/**
	 * Downloads invoice Action
	 *
	 * @Route("/download/{id}", name="invoice_download", requirements={"id"="\d+"}, methods="GET")
	 * @Entity("order", expr="repository.findOrderByID(id)")
	 *
	 * @param Order $order
	 * @return BinaryFileResponse
	 */
    public function downloadPDF(Order $order)
    {
        $invoice = $order->findOneInvoiceByType([Invoice::ORDINARY, Invoice::ADDITIONAL, Invoice::MONTHLY_PER_USER, Invoice::MONTHLY_PER_ORGANIZATION, Invoice::WALLET, Invoice::RECHARGE]);
        $targetPDF = join(DIRECTORY_SEPARATOR, [$this->getParameter('fpdf_config')['media'], $invoice->getPaymentDate()->format('Y-m-d'), $invoice->getPdfFileName()]);

        $info = new \SplFileInfo($targetPDF);
        if (!$info->isFile()) {
            $event = new MissingInvoicePdfEvent($invoice);
            $this->dispatch(Events::ON_MISSING_INVOICE_PDF, $event);
        }

        return $this->file($info->getRealPath(), $invoice->getPdfFileName(), ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }
	
	/**
	 * Downloads done ZIP Action
	 *
	 * @Route("/done/zip/{id}", requirements={"id"="\d+"}, name="order_done_zip", methods="GET")
	 *
	 * @param Order $order
	 * @param OrderArchiveHandler $orderArchiveHandler
	 * @return BinaryFileResponse
	 */
    public function downloadDoneZIP(Order $order, OrderArchiveHandler $orderArchiveHandler): BinaryFileResponse
    {
	    if (in_array($order->getOrderStatus(), [Order::COMPLETED])){
		    $this->createAccessDeniedException('order status is not valid');
	    }
	    
        return $this->file($orderArchiveHandler->createDoneArchive($order));
    }
}
