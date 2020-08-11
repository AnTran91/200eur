<?php

namespace App\Controller\Immosquare;

use App\Controller\Immosquare\Traits\APIControllerTrait;
use App\Controller\Immosquare\Traits\OrderEventControllerTrait;

use App\Controller\Immosquare\RequestFilter\APIFilterInterface;
use App\Entity\Field;
use App\Form\Immosquare\OrderType;

use App\Handlers\OrderHandler;
use App\Utils\Events;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Security("is_granted('ROLE_IMMOSQUARE_USER')")
 */
class OrderCreationController extends Controller implements APIFilterInterface
{
    use APIControllerTrait, OrderEventControllerTrait;
	
	/**
	 * @Route(name="api_create_order", path="/create/order.{_format}", requirements={"_format": "json|xml"}, defaults={"_format": "json"}, methods="POST")
	 *
	 * @param Request $request
	 * @param SerializerInterface $serializer
	 * @param OrderHandler $orderHandler
	 * @return Response
	 */
    public function createOrder(Request $request, SerializerInterface $serializer, OrderHandler $orderHandler)
    {
        $form = $this->createForm(OrderType::class);
        $form->submit($request->request->all());

        if ($form->isValid()) {

            $order = $this->fireOrderEvent([
                'data' => $form->getData(),
                'event_name' => Events::ON_CREATE_IMMOSQUARE_ORDER
            ]);

            $data = $serializer->serialize($orderHandler->formatCreatedOrder($order), $request->getRequestFormat());
            return $this->createResponse($data, Response::HTTP_CREATED, $request->getMimeType($request->getRequestFormat()));
        }

        $this->throwApiProblemValidationException($form);
	
	    throw new \LogicException('none reachable bloc');
    }
}