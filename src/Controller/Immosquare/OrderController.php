<?php

namespace App\Controller\Immosquare;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

use App\Controller\Immosquare\Traits\APIControllerTrait;

use App\Entity\ApplicationType;
use App\Entity\Order;

use App\Handlers\OrderHandler;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("is_granted('ROLE_IMMOSQUARE_USER')")
 */
class OrderController extends Controller
{
    use APIControllerTrait;
	
	/**
	 * @Route(name="api_fetch_order", path="/fetch/{order_id}/order.{_format}", requirements={"_format": "json|xml"}, defaults={"_format": "json"}, methods={"GET"})
	 * @Entity("order", expr="repository.findByIdOrOrderNumber(order_id)")
	 *
	 * @param Request $request
	 * @param Order $order
	 * @param SerializerInterface $serializer
	 * @param OrderHandler $orderHandler
	 * @return Response
	 */
    public function show(Request $request, Order $order, SerializerInterface $serializer, OrderHandler $orderHandler)
    {
        if ($order->getAppType() == ApplicationType::IMMOSQUARE_TYPE && in_array($order->getOrderStatus(), [Order::COMPLETED]))
        {
            $data = $serializer->serialize($orderHandler->formatReadyOrder($order), $request->getRequestFormat());
            return $this->createResponse($data, Response::HTTP_OK, $request->getMimeType($request->getRequestFormat()));
        }

        $this->throwApiProblemOrderNotReadyException($order);
        
        throw new \LogicException('none reachable bloc');
    }

}