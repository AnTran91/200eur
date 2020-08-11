<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ControllerTrait;
use App\Repository\PictureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Handlers\OrderArchiveHandler;
use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Form\Admin\OrderType;
use App\Form\Admin\Filters\OrderFilterType;
use App\Handlers\OrderHandler;

use Doctrine\DBAL\Exception as DBException;

use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("order")
 * @Security("is_granted('ROLE_ORDER_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class OrderController extends Controller
{
    use ControllerTrait;

	/**
	 * @Route("/", name="admin_order_index", methods="GET")
	 *
	 * @param Request $request
	 * @return Response
	 * @throws \Exception
	 */
    public function index(Request $request): Response
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('soft_deleteable');

        $repository = $this->getDoctrine()->getRepository(Order::class);
        $filter = $request->query->get('filter', []);
        $itemsPerPage = 20;
        if (!empty($filter['itemsPerPage'])) {
            $itemsPerPage = $filter['itemsPerPage'] == 'all' ? $repository->count([]) : $filter['itemsPerPage'];
        }

        $this->changeItemsPerPage($itemsPerPage);
        
        $session = new Session();
        if (isset($filter['isRecharge'])) {
            if ($filter['isRecharge'] === "1") {
                $session->set('isRecharge', 1);
            }
            if ($filter['isRecharge'] === "0") {
                $session->set('isRecharge', 0);
            }
        }
        else {
            if ($session->get('isRecharge') === 1) {
                $filter['isRecharge'] = "1";
            }
            if ($session->get('isRecharge') === 0) {
                $filter['isRecharge'] = "0";
            }
        }

        $orders = $this->dynamicPaginator(
        	[
        	    'filter' => $filter,
                'page' => $request->query->get('page', 1),
            ],
	        Order::class,
	        ['client', 'pictures', 'invoices', 'production', 'affectedTo', 'deliveryTime', 'transaction', 'promotion'],
	        ['DEFAULT_SORT_FIELD_NAME' => 'orderNumber']
        );

        if ($request->isXmlHttpRequest()) {
            return $this->json([
              'html' => $this->renderView('admin/order/_list.html.twig', ['orders' => $orders]),
              'page' => $orders->getPaginationData()['current'],
              'params' => $orders->getParams(),
              'total_page' => $orders->getPaginationData()['pageCount']
            ]);
        }

        return $this->render('admin/order/index.html.twig', [
          'orders' => $orders,
          'filter_form' => $this->createForm(OrderFilterType::class)->createView()
        ]);
    }

	/**
	 * @Route("/show/{id}", name="admin_order_show", methods="GET")
	 * @Entity("order", expr="repository.findOrderByID(id)")
	 * @Cache(lastModified="order.getUpdatedAt()", ETag="'OrderCreation' ~ order.getId() ~ order.getUpdatedAt().getTimestamp()")
	 *
	 * @param Request $request
	 * @param Order $order
	 * @param PictureRepository $pictureRepository
	 * @return Response
	 */
    public function show(Request $request, Order $order, PictureRepository $pictureRepository, OrderArchiveHandler $orderArchiveHandler): Response
    {
        $pictures = $this->basicPaginator([
          'page' => $request->query->get('page', 1)
        ], $pictureRepository->getQueryFindByOrder($order->getId()));
        $zipFilename = '';
        $zipPathname = '';
        $containAtLeastOne = 0;
        foreach ($pictures as $picture) {
            if ($picture !== null) {
                if ($picture->getPictureDetail() !== null) {
                    foreach ($picture->getPictureDetail() as $pictureDetail) {
                        if ($pictureDetail !== null) {
                            if ($pictureDetail->getReturnedPicture() !== null) {
                                if ($pictureDetail->getReturnedPicture()->getPaintedPicturePath() !== null) {
                                    $containAtLeastOne = 1;
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($containAtLeastOne === 1) {
            $zip = $this->file($orderArchiveHandler->createIllustrativeArchive($order));
            $zipFilename = $zip->getFile()->getFilename();
            $zipPathname = substr($zip->getFile()->getPathname(), strpos($zip->getFile()->getPathname(), '/uploads'));
        }
        return $this->render('admin/order/show.html.twig', [
          'order' => $order,
          'pictures' => $pictures,
          'zipFilename' => $zipFilename,
          'zipPathname' => $zipPathname
        ]);
    }

	/**
	 * @Route("/edit/{id}", name="admin_order_edit", methods="GET|POST")
	 * @Entity("order", expr="repository.findOrderByID(id)")
	 *
	 * @param Request $request
	 * @param Order $order
	 * @param OrderHandler $orderHandler
	 * @return Response
	 * @throws \Doctrine\DBAL\ConnectionException
	 */
    public function edit(Request $request, Order $order, OrderHandler $orderHandler): Response
    {
        $form = $this->createForm(OrderType::class, $order)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->getClickedButton() && 'updatePrice' === $form->getClickedButton()->getName()) {
                if ($order->getIsChange() == false) {
                    $order->setIsChange(true);
                }
                $order = $orderHandler->updateOrderPrice($order);
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('flash_msg_success', $this->trans('admin.order.msg.edit', [], 'admin'));
        }
        
        return $this->render('admin/order/edit.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

	/**
	 * Bulk delete action.
	 *
	 * @Route("/bulk/delete", name="admin_order_bulk_delete", methods="DELETE")
	 *
	 * @param Request $request
	 * @param OrderRepository $orderRepository
	 * @return Response
	 */
    public function bulkDeleteAction(Request $request, OrderRepository $orderRepository): Response
    {
        $isAjax = $request->isXmlHttpRequest();

        $data = $request->request->get('data', []);
        $token = $request->request->get('token');

        if ($isAjax && !empty($data) && !empty($token)) {
            if (!$this->isCsrfTokenValid('multiselect_orders', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }

            if ($orderRepository->removeOrUndoOrdersByIds($data)){
	            return $this->json(['msg' => $this->trans('admin.order.msg.delete', [], 'admin'), 'success' => true]);
            }
        }

        return $this->json(['msg' => $this->trans('admin.common.error_msg', [], 'admin'), 'success' => false]);
    }

	/**
	 * Bulk delete action.
	 *
	 * @Route("/bulk/undelete", name="admin_order_bulk_undelete", methods="DELETE")
	 *
	 * @param Request $request
	 * @param OrderRepository $orderRepository
	 * @return Response
	 */
    public function bulkUnDeleteAction(Request $request, OrderRepository $orderRepository): Response
    {
        $isAjax = $request->isXmlHttpRequest();

        $data = $request->request->get('data', []);
        $token = $request->request->get('token');

        if ($isAjax && !empty($data) && !empty($token)) {
            if (!$this->isCsrfTokenValid('multiselect_orders', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }

	        if ($orderRepository->removeOrUndoOrdersByIds($data, false)){
	        	return $this->json(['msg' => $this->trans('admin.order.msg.undelete', [], 'admin'), 'success' => true]);
	        }
        }

        return $this->json(['msg' => $this->trans('admin.common.error_msg', [], 'admin'), 'success' => false]);
    }

    /**
     * get external image.
     *
     * @Route("/load-external-image/", name="load_external_image", methods="GET|POST")
     *
     * @return Response
     */
    public function loadExternalImage(): Response
    {
        $url = $_GET["url"];                     // the image URL
        $info = getimagesize($url);              // get image data
        header("Content-type: ". $info['mime']); // act as image with right MIME type
        readfile($url);                          // read binary image data
        die();
    }
}
