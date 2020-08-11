<?php

namespace App\Controller;

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

class transactionControllerTest extends Controller
{
	use ControllerTrait;
    use OrderEventControllerTrait;
    use SystemPayControllerTrait;
	/**
	 * After the redirection to the payment system form
	 * - validate the transaction.
	 * - update the order status (depend on the transaction status).
	 *
	 * @Route("/transaction/validation/transaction-response-test", name="transaction_response_test", methods={"GET", "POST"})
	 *
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
    public function validateTransactionResponseTest(Request $request)
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
}