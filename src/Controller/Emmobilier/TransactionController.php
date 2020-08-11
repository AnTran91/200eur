<?php

namespace App\Controller\Emmobilier;

use App\Controller\Emmobilier\Traits\ControllerTrait;
use App\Controller\Emmobilier\Traits\OrderEventControllerTrait;
use App\Controller\Emmobilier\Traits\SystemPayControllerTrait;
use App\Entity\Transaction;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use App\Entity\Order;
use App\Entity\Wallet;

use App\Utils\Events;

/**
 * @Route("/transaction")
 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class TransactionController extends Controller
{
    use ControllerTrait;
    use OrderEventControllerTrait;
    use SystemPayControllerTrait;
	
	/**
	 * Pay the order with Payment card (system pay)
	 * - we are making a redirection to the system pay form
	 *
	 * @Route("/make/transaction/{id}", name="pay_saved_order_transaction", methods="GET")
	 *
	 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN') && order.isPayed()")
	 *
	 * @param Order $order
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function payWithPaymentCard(Order $order)
    {
        return $this->sendPaymentRequest([
            'amount' => $order->getAmountIncludingTaxAfterReduction(),
            'orderID' => sprintf("%04d", $order->getId()),
            'reference' => sprintf("Order ID (%04d)", $order->getId()),
            'customer' => $this->getUser(),
            'validationURL' => $this->generateUrl('validation_order_transaction_response', [], UrlGeneratorInterface::ABSOLUTE_URL)
        ]);
    }
	
	/**
	 * After the redirection to the payment system form
	 * - validate the transaction.
	 * - update the order status (depend on the transaction status).
	 *
	 * @Route("/validation/transaction/{id}", name="validation_order_transaction", methods={"GET", "POST"})
	 *
	 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN') && order.isPayed()")
	 *
	 * @param Request $request
	 * @param Order $order
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
    public function validateTransaction(Request $request, Order $order)
    {
        $response = $this->paymentVerification($request->request->all(), $request->getLocale());
        if ($response['VALIDATION']) {
	        $this->fireOrderEvent([
		        'order' => $order,
		        'transaction' => $response['TRANSACTION'],
		        'event_name' => Events::ON_PAY_ORDER_BY_TRANSACTION,
	        ]);
	
	        $this->addFlash('flash_msg_success', $this->trans('orders.msg.new_transaction'));
        }elseif ($response['STATUS'] == Transaction::ABANDONED){
	        $this->addFlash('flash_msg_error', $this->trans('orders.msg.error_transaction'));
	    }else{
	        $order->setOrderStatus(Order::ERROR_CB);
	        $this->getDoctrine()->getManager()->flush();
	        
	        $this->addFlash('flash_msg_error', $response['COMMENT']);
        }
        
        return $this->redirectToRoute('order_list');
    }

    /**
	 * After the redirection to the payment system form
	 * - validate the transaction.
	 * - update the order status (depend on the transaction status).
	 *
	 * @Route("/validation/transaction-response", name="validation_order_transaction_response", methods={"GET", "POST"})
	 *
	 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN')")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
    public function validateTransactionResponse(Request $request)
    {
		$request_all = $request->request->all();
		if (!empty($request_all)) {
			$response = $this->paymentVerification($request_all, $request->getLocale());
			$order_id = (int) $request_all['vads_order_id'];
			$order = $this->getDoctrine()
				          ->getRepository(Order::class)
				          ->find($order_id);

			if ($order->isPayed()) {
				if ($response['VALIDATION']) {
			        $this->fireOrderEvent([
				        'order' => $order,
				        'transaction' => $response['TRANSACTION'],
				        'event_name' => Events::ON_PAY_ORDER_BY_TRANSACTION,
			        ]);
			
			        $this->addFlash('flash_msg_success', $this->trans('orders.msg.new_transaction'));
		        }elseif ($response['STATUS'] == Transaction::ABANDONED){
			        $this->addFlash('flash_msg_error', $this->trans('orders.msg.error_transaction'));
			    }else{
			        $order->setOrderStatus(Order::ERROR_CB);
			        $this->getDoctrine()->getManager()->flush();
			        
			        $this->addFlash('flash_msg_error', $response['COMMENT']);
		        }
			}
		}
        
		return $this->redirectToRoute('order_list');
    }
	
	/**
	 * Validate wallet transaction Action
	 *
	 * Fill the wallet with Payment card (system pay)
	 * - we are making a redirection to the system pay form
	 *
	 * @Route("/new", name="new_wallet_transaction", methods="GET")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function newWalletTransaction(Request $request)
    {
        if (!$this->isCsrfTokenValid('update_wallet'.$this->getUser()->getWallet()->getId().$request->query->get('amount', 0), $request->query->get('_token'))) {
            throw $this->createNotFoundException('Invalid or missing CSRF token');
        }

        return $this->sendPaymentRequest([
            'amount' => floatval($request->query->get('amount')),
            'orderID' => sprintf("%04d", $this->getUser()->getWallet()->getId()),
            'reference' => sprintf("Wallet ID (%04d)", $this->getUser()->getWallet()->getId()),
            'customer' => $this->getUser(),
            'validationURL' => $this->generateUrl('validate_wallet_transaction', ['id' => $this->getUser()->getWallet()->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
        ]);
    }
	
	/**
	 * Validate wallet transaction Action
	 *
	 * After the redirection to the payment system form
	 * - validate the transaction.
	 * - update the wallet (depend on the transaction status).
	 *
	 * @Route("/validate/{id}", name="validate_wallet_transaction", methods="POST")
	 *
	 * @param Request $request
	 * @param Wallet $wallet
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
    public function validateWalletTransaction(Request $request, Wallet $wallet)
    {
        $response = $this->paymentVerification($request->request->all(), $request->getLocale());
        if ($response['VALIDATION']) {
            /** @var Transaction $transaction */
            $transaction = $response['TRANSACTION'];
            
            $transaction->setWallet($this->getUser()->getWallet());
            $wallet->addTransaction($transaction);
            $amount = 0;
            if ($this->getUser()->getBillingAddress()->getCountry() === "FR") {
            	$amount = $transaction->getAmount() * 5 / 6;
            } else {
            	$amount = $transaction->getAmount();
            }
            $wallet->increaseAmount($transaction->getAmount());

            $this->getDoctrine()->getManager()->flush();
            
            $order = new Order();
            $order->setOrderNumber((int)++$this->getDoctrine()->getRepository(Order::class)->findLastOrderNumber()['lastNumber']);
            $order->setTotalAmount($amount);
            $order->setClient($this->getUser());
            $order->setCreationDate(new \DateTime('now'));
            $order->setPaymentDate(new \DateTime('now'));
            $order->setOrderStatus('order.status.approvisionnement_tirelire');
            $order->setSendEmail(false);
            $order->setIsChange(false);
            $order->setIsRecharge(true);
            $order->setAppType('application.type.emmobilier');
            $order->setTransaction($transaction);
            $order->setTaxPercentage($this->getUser()->getBillingAddress()->getCountry() === "FR" ? 20 : 0);

						$this->fireOrderEvent([
							'order' => $order,
							'event_name' => Events::ON_RECHARGE_TO_WALLET
						]);
            $this->addFlash('flash_msg_success', $this->trans('transaction.wallet.transaction_success'));
        }else{
	        $this->addFlash('flash_msg_error', $response['COMMENT']);
        }
	
	    if (!$this->getOrderHandler()->validate($this->getUser()->getUserDirectory())) {
		    return $this->redirectToRoute('order_recap');
	    }
        
        return $this->redirectToRoute('order_new');
	}
	
	// /**
    //  * Pay the order with Payment card (system pay)
    //  * - we are making a redirection to the system pay form
    //  *
    //  * @Route("/make/transaction-addition/{id}", name="make_addition_order_transaction", methods="GET")
    //  *
    //  * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN') && order.isPayed()")
    //  *
    //  * @param Order $order
    //  * @return \Symfony\Component\HttpFoundation\Response
    //  */
    // public function payAdditionWithPaymentCard(Order $order)
    // {
    //     $amount = 0;
    //     foreach ($order->getPictures() as $picture){
    //         foreach ($picture->getPictureDetail() as $pictureDetail){
    //             $amount += $pictureDetail->getAdditionPrice();
    //         }
    //     }
    //     $amount += $amount * $order->getTaxPercentage() / 100;
    //     return $this->sendPaymentRequest([
    //         'amount' => $amount,
    //         'orderID' => sprintf("%04d", $order->getId()),
    //         'reference' => sprintf("Order ID (%04d)", $order->getId()),
    //         'customer' => $this->getUser(),
    //         'validationURL' => $this->generateUrl('validation_addition_order_transaction_response', [], UrlGeneratorInterface::ABSOLUTE_URL)
    //     ]);
    // }

    // /**
    //  * @Route("/validation-addition/transaction-response", name="validation_addition_order_transaction_response", methods={"GET", "POST"})
    //  *
    //  * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN')")
    //  *
    //  * @param Request $request
    //  * @return \Symfony\Component\HttpFoundation\RedirectResponse
    //  */
    // public function validateAdditionOrderTransactionResponse(Request $request)
    // {
    //     $request_all = $request->request->all();
    //     if (!empty($request_all)) {
    //         $response = $this->paymentVerification($request_all, $request->getLocale());
    //         $order_id = (int) $request_all['vads_order_id'];
    //         $order = $this->getDoctrine()
    //                       ->getRepository(Order::class)
    //                       ->find($order_id);

    //         if ($response['VALIDATION']) {
    //             foreach ($order->getPictures() as $picture){
    //                 foreach ($picture->getPictureDetail() as $pictureDetail){
    //                     $pictureDetail->setAdditionPrice(0);
    //                 }
    //             }
    //             $this->fireOrderEvent([
    //                 'order' => $order,
    //                 'transaction' => $response['TRANSACTION'],
    //                 'event_name' => Events::ON_PAY_ADDITION_BY_TRANSACTION,
    //             ]);
    //             $order->setIsChange(false);
    //             $this->getDoctrine()->getManager()->flush();
    //             $this->addFlash('flash_msg_success', $this->trans('transaction.wallet.transaction_success'));
    //         }elseif ($response['STATUS'] == Transaction::ABANDONED){
    //             $this->addFlash('flash_msg_error', $this->trans('orders.msg.error_transaction'));
    //         }else{
    //             $this->addFlash('flash_msg_error', $response['COMMENT']);
    //         }
    
    //     }
    //     return $this->redirectToRoute('order_list');
    // }
}
