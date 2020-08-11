<?php
namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ControllerTrait;
use App\Entity\OrderDeliveryTime;
use App\Form\Admin\DeliveryTimeType;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("delivery_time")
 * @Security("is_granted('ROLE_DELIVERY_TIME_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class DeliveryTimeController extends Controller
{
    use ControllerTrait;
	
	/**
	 * @Route("/", name="admin_delivery_time_list", methods="GET")
	 *
	 * @param Request $request
	 * @return Response
	 * @throws \Exception
	 */
    public function index(Request $request)
    {
        $deliveryTimes = $this->dynamicPaginator([
          'search' => $request->query->get('search', null),
          'page' => $request->query->get('page', 1)
        ], OrderDeliveryTime::class);

        return $this->render('admin/delivery_time/index.html.twig', [
          'deliveryTimes' => $deliveryTimes
        ]);
    }
	
	/**
	 * @Route("/new", name="admin_delivery_time_new", methods="GET|POST")
	 *
	 * @param Request $request
	 * @return Response
	 */
    public function new(Request $request): Response
    {
        $deliveryTime = new OrderDeliveryTime();
        $form = $this->createForm(DeliveryTimeType::class, $deliveryTime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($deliveryTime);
            $em->flush();

            $this->addFlash('flash_msg_success', $this->trans('admin.delivery_time.flash.created', [], 'admin'));

            return $this->redirectToRoute('admin_delivery_time_list');
        }

        return $this->render('admin/delivery_time/new.html.twig', [
           'deliveryTime' => $deliveryTime,
           'form' => $form->createView(),
       ]);
    }
	
	
	/**
	 * @Route("/{id}/edit", name="admin_delivery_time_edit", methods="GET|POST")
	 *
	 * @param Request $request
	 * @param OrderDeliveryTime $deliveryTime
	 * @return Response
	 */
    public function edit(Request $request, OrderDeliveryTime $deliveryTime): Response
    {
        $form = $this->createForm(DeliveryTimeType::class, $deliveryTime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('flash_msg_success', $this->trans('admin.delivery_time.flash.updated', [], 'admin'));

            return $this->redirectToRoute('admin_delivery_time_edit', ['id' => $deliveryTime->getId()]);
        }

        return $this->render('admin/delivery_time/edit.html.twig', [
       'deliveryTime' => $deliveryTime,
       'form' => $form->createView(),
     ]);
    }
	
	/**
	 * @Route("/{id}", name="admin_delivery_time_show", methods="GET")
	 *
	 * @param OrderDeliveryTime $deliveryTime
	 * @return Response
	 */
    public function show(OrderDeliveryTime $deliveryTime): Response
    {
        return $this->render('admin/delivery_time/show.html.twig', ['deliveryTime' => $deliveryTime]);
    }
	
	/**
	 * @Route("/{id}", name="admin_delivery_time_delete", methods="POST")
	 *
	 * @param Request $request
	 * @param OrderDeliveryTime $deliveryTime
	 * @return Response
	 */
    public function delete(Request $request, OrderDeliveryTime $deliveryTime): Response
    {
        if ($this->isCsrfTokenValid('delete'.$deliveryTime->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($deliveryTime);
            $em->flush();

            $this->addFlash('flash_msg_success', $this->trans('admin.delivery_time.flash.deleted', [], 'admin'));
        }

        return $this->redirectToRoute('admin_delivery_time_list');
    }
}
