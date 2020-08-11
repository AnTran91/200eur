<?php

namespace App\Controller\Emmobilier;

use App\Controller\Emmobilier\Traits\ControllerTrait;
use App\Controller\Emmobilier\Traits\OrderEventControllerTrait;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Utils\Events;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/pay")
 * @Security("is_granted('ROLE_EMMOBILIER_USER')")
 */
class OrderActionsController extends Controller
{
    use ControllerTrait;
    use OrderEventControllerTrait;
	
	/**
	 * Save OrderCreation action.
	 *
	 * @Route("/later", name="pay_later_action", methods="POST")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
    public function payLater(Request $request)
    {
        // 'create-order' is the same value used in the template to generate the token
        if (!$this->isCsrfTokenValid('create-pay-later-order', $request->request->get('_token'))) {
            return $this->json(['success' => false, 'msg' => $this->trans('orders.msg.token_error')]);
        }

        $this->addFlash('flash_msg_success', $this->trans('orders.msg.new_pay_later'));

        /*$order = */$this->fireOrderEvent([
          'event_name' => Events::ON_SAVE_ORDER
        ]);

        return $this->json([
            'success' => true,
            'targetURL' =>  $this->generateUrl('order_list',[], UrlGeneratorInterface::ABSOLUTE_URL)
	        /*$this->generateUrl('order_show', [
                'id' => $order->getId()
            ], UrlGeneratorInterface::ABSOLUTE_URL)*/
        ]);
    }
	
	/**
	 * Pay with the wallet OrderCreation action.
	 *
	 * @Route("/wallet", name="pay_with_wallet_action", methods="POST")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
	 */
    public function payWithWallet(Request $request)
    {
        // 'create-order' is the same value used in the template to generate the token
        if (!$this->isCsrfTokenValid('create-order', $request->request->get('_token'))) {
            return $this->json(['success' => false, 'msg' => $this->trans('orders.msg.token_error')]);
        }

        $order = $this->fireOrderEvent([
          'event_name' => Events::ON_SAVE_ORDER
        ]);

        return $this->forward('App\Controller\Emmobilier\OrderPaymentController::payNowWithWallet', [
            'id'  => $order->getId()
        ]);
    }
	
	/**
	 * Pay with the wallet OrderCreation action.
	 *
	 * @Route("/free/order", name="free_order_action", methods="POST")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
	 */
    public function freeOrder(Request $request)
    {
        // 'create-order' is the same value used in the template to generate the token
        if (!$this->isCsrfTokenValid('create-free-order', $request->request->get('_token'))) {
            return $this->json(['success' => false, 'msg' => $this->trans('orders.msg.token_error')]);
        }

        $order = $this->fireOrderEvent([
          'event_name' => Events::ON_SAVE_ORDER
        ]);

        return $this->forward('App\Controller\Emmobilier\OrderPaymentController::freeOrder', [
            'id'  => $order->getId()
        ]);
    }
	
	/**
	 * New order transaction Action
	 *
	 * @Route("/now", name="pay_now_action", methods="POST")
	 *
	 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN')")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
    public function payNow(Request $request)
    {
        // 'create-order' is the same value used in the template to generate the token
        if (!$this->isCsrfTokenValid('create-order', $request->request->get('_token'))) {
            return $this->json(['success' => false, 'msg' => $this->trans('orders.msg.token_error')]);
        }

        $order = $this->fireOrderEvent([
          'event_name' => Events::ON_SAVE_ORDER
        ]);

        return $this->json([
            'success' => true,
            'targetURL' => $this->generateUrl('pay_saved_order_transaction', [
                'id' => $order->getId()
            ], UrlGeneratorInterface::ABSOLUTE_URL)
        ]);
    }
	
	/**
	 * New monthly order Action
	 *
	 * @Route("/monthly", name="pay_monthly_action", methods="POST")
	 *
	 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN')")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
	 */
    public function monthlyOrder(Request $request)
    {
        // 'create-order' is the same value used in the template to generate the token
        if (!$this->isCsrfTokenValid('create-monthly-order', $request->request->get('_token'))) {
            return $this->json(['success' => false, 'msg' => $this->trans('orders.msg.token_error')]);
        }

        $order = $this->fireOrderEvent([
          'event_name' => Events::ON_SAVE_ORDER
        ]);

        return $this->forward('App\Controller\Emmobilier\OrderPaymentController::monthlyOrder', [
            'id'  => $order->getId()
        ]);
    }
}
