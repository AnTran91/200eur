<?php

namespace App\Controller\Emmobilier;

use App\Controller\Emmobilier\Traits\ControllerTrait;
use App\Controller\Emmobilier\Traits\OrderEventControllerTrait;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Order;

use App\Utils\Events;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/order")
 * @Security("is_granted('ROLE_EMMOBILIER_USER')")
 */
class OrderPaymentController extends Controller
{
    use ControllerTrait;
    use OrderEventControllerTrait;
	
	/**
	 * Pay the order with user wallet.
	 * - upadate the order status
	 * - update the user wallet
	 *
	 * @Route("/now_with_wallet/{id}", name="pay_now_with_wallet_action", methods="POST")
	 *
	 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN') && order.isPayed()")
	 *
	 * @param Order $order
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
    public function payNowWithWallet(Order $order)
    {
        $order = $this->fireOrderEvent([
          'order' => $order,
          'event_name' => Events::ON_PAY_ORDER_BY_WALLET
        ]);

        $this->addFlash('flash_msg_success', $this->trans('orders.msg.new_wallet'));

        return $this->json([
            'success' => true,
            'targetURL' => $this->generateUrl('order_show', [
                'id' => $order->getId()
            ], UrlGeneratorInterface::ABSOLUTE_URL)
        ]);
    }
	
	/**
	 * Free order.
	 * - upadate the order status
	 *
	 * @Route("/free/order/{id}", name="save_free_order_action", methods="POST")
	 *
	 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN') && order.isPayed() && order.getAmountIncludingTaxAfterReduction() == 0")
	 *
	 * @param Order $order
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
    public function freeOrder(Order $order)
    {
        $order = $this->fireOrderEvent([
          'order' => $order,
          'event_name' => Events::ON_FREE_ORDER
        ]);

        $this->addFlash('new_transaction', $this->trans('orders.msg.new_monthly_invoice'));

        return $this->json([
            'success' => true,
            'targetURL' => $this->generateUrl('order_show', [
                'id' => $order->getId()
            ], UrlGeneratorInterface::ABSOLUTE_URL)
        ]);
    }
	
	/**
	 * New monthly order Action
	 * Free order.
	 * - upadate the order status
	 *
	 * @Route("/monthly/{id}", name="pay_order_monthly_action", methods="POST")
	 *
	 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN') && order.isPayed()")
	 *
	 * @param Order $order
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
    public function monthlyOrder(Order $order)
    {
        $order = $this->fireOrderEvent([
          'order' => $order,
          'event_name' => Events::ON_SAVE_MONTHLY_ORDER
        ]);

        $this->addFlash('new_transaction', $this->trans('orders.msg.new_monthly_invoice'));

        return $this->json([
            'success' => true,
            'targetURL' => $this->generateUrl('order_show', [
                'id' => $order->getId()
            ], UrlGeneratorInterface::ABSOLUTE_URL)
        ]);
    }
}
