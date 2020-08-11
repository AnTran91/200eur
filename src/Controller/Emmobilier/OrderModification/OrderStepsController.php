<?php
/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Emmobilier\OrderModification;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

use App\Entity\Order;

use App\Form\Emmobilier\OrderDeliveryTimeType;

/**
 * OrderCreation Creation steps Controller
 *
 * @Route("/edit/step")
 *
 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class OrderStepsController extends Controller
{
    use \App\Controller\Emmobilier\Traits\ControllerTrait;
	
	/**
	 * Upload Step
	 *
	 * @Route("/upload/{id}", requirements={"id"="\d+"}, name="order_edit", methods="GET")
	 * @Cache(maxage="86400", smaxage="86400")
	 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN') && order.isPayed()")
	 * @Entity("order", expr="repository.findOrderByID(id)")
	 *
	 * @param Order $order
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function uploadPage(Order $order)
    {
        return $this->render('emmobilier/order_modification/upload.html.twig', [
          'order' => $order
        ]);
    }
	
	/**
	 * Recap Step
	 *
	 * @Route("/recap/{order_id}", requirements={"id"="\d+"}, name="order_recap_edit", methods="GET")
	 * @Cache(maxage="86400", smaxage="86400")
	 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN') && order.isPayed()")
	 * @Entity("order", expr="repository.findOrderByID(order_id)")
	 *
	 * @param Order $order
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function recapPage(Order $order)
    {
        $response = $this->getOrderHandler()->getFormattedOrder($order);

        return $this->render('emmobilier/order_modification/recap.html.twig', [
          'orderNum' => sprintf("%04d", $this->getOrderHandler()->getTheLastOrderNumber()),
          'response' => $response,
          'order' => $order
        ]);
    }
	
	/**
	 * Update The Retouch Price List.
	 *
	 * @Route("/edit/delivery/{id}", requirements={"id"="\d+"}, name="edit_order_delivery_time", methods="POST")
	 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN') && order.isPayed()")
	 *
	 * @param Request $request
	 * @param Order $order
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 * @throws \Doctrine\DBAL\ConnectionException
	 */
    public function updateTheRetouchPriceList(Request $request, Order $order)
    {
        $form = $this->createForm(OrderDeliveryTimeType::class, null)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order = $this->getOrderHandler()->updateOrderPrice($order, $form->getData());
            $this->getDoctrine()->getManager()->flush();
        }
        $formattedOrder = $this->getOrderHandler()->getFormattedOrder($order);

        return $this->json([
          'response' => $this->renderView('emmobilier/_shared_components/_uploaded_pictures_recap.html.twig', ['response' => $formattedOrder]),
          'actions' => $this->renderView('emmobilier/order_modification/_payment_actions.html.twig', ['order' => $order, 'response' => $formattedOrder, 'user' => $this->getUser()]),
        ]);
    }
	
	/**
	 * Render delivery time.
	 *
	 * @param Order $order
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function renderDeliveryTime(Order $order)
    {
        $form = $this->createForm(OrderDeliveryTimeType::class, null, [
          'action' => $this->generateUrl('edit_order_delivery_time', ['id' => $order->getId()])
        ])->submit(['deliveryTime' => $order->getDeliveryTime()->getId()]);

        return $this->render('emmobilier/_shared_components/_delivery_time_form.html.twig', [
            'form' => $form->createView(),
            'display_form' => $order->getDeliveryTime()->getRetouchPrice()->count() !== 1
        ]);
    }
}
