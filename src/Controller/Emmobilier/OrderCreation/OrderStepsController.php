<?php
/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Emmobilier\OrderCreation;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Form\Emmobilier\OrderDeliveryTimeType;

/**
 * OrderCreation Creation steps Controller
 *
 * @Route("/create/step")
 *
 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class OrderStepsController extends Controller
{
    use \App\Controller\Emmobilier\Traits\ControllerTrait;
	
	/**
	 * Upload Step
	 *
	 * @Route("/upload", name="order_new", methods="GET")
	 * @Cache(maxage="86400", smaxage="86400")
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function uploadPage()
    {
        return $this->render('emmobilier/order_creation/upload.html.twig');
    }
	
	/**
	 * Recap Step
	 *
	 * @Route("/recap", name="order_recap", methods="GET")
	 * @Cache(maxage="86400", smaxage="86400")
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function recapPage()
    {
        if ($this->getOrderHandler()->validate($this->getUser()->getUserDirectory())) {
            $this->addFlash('flash_msg_success', $this->trans('uploader.msg.setting.validate'));
            return $this->redirectToRoute('order_new');
        }
	
	
	    $this->getOrderHandler()->initDeliveryTime();
        $response = $this->getOrderHandler()->saveAndDisplayTemporaryOrder($this->getUser());

        return $this->render('emmobilier/order_creation/recap.html.twig', [
          'orderNum' => sprintf("%04d", $this->getOrderHandler()->getTheLastOrderNumber()),
          'response' => $response
        ]);
    }
	
	/**
	 * Update The Retouch Price List.
	 *
	 * @Route("/delivery/time", name="create_order_delivery_time", methods="POST")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function updateTheRetouchPriceList(Request $request)
    {
        $form = $this->createForm(OrderDeliveryTimeType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $request->getSession()->set('deliveryTime', $form->get('deliveryTime')->getData());
        }

        $response = $this->getOrderHandler()->saveAndDisplayTemporaryOrder($this->getUser(), (array) $form->getData());

        return $this->json([
          'response' => $this->renderView('emmobilier/_shared_components/_uploaded_pictures_recap.html.twig', ['response' => $response]),
          'actions' => $this->renderView('emmobilier/order_creation/_payment_actions.html.twig', ['response' => $response, 'user' => $this->getUser()]),
        ]);
    }
	
	/**
	 * Render delivery time.
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function renderDeliveryTime(Request $request)
    {
        $session = $request->getSession();

        $form = $this->createForm(OrderDeliveryTimeType::class, null, [
          'action' => $this->generateUrl('create_order_delivery_time')
        ])->submit(['deliveryTime' => is_null($session->get('deliveryTime')) ? null : $session->get('deliveryTime')->getId()]);

        return $this->render('emmobilier/_shared_components/_delivery_time_form.html.twig', [
            'form' => $form->createView(),
            'display_form' => !$this->getOrderHandler()->doPicturesHasMultiplePrices($this->getUser())
        ]);
    }
	
	/**
	 * Payment step
	 *
	 * @Route("/transaction", name="order_transaction", methods={"GET", "POST"})
	 *
	 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN')")
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function paymentPage()
    {
        if ($this->getOrderHandler()->validate($this->getUser()->getUserDirectory())) {
            throw $this->createNotFoundException($this->trans('uploader.msg.setting.validate'));
        }

        $response = $this->getOrderHandler()->saveAndDisplayTemporaryOrder($this->getUser());

        return $this->render('emmobilier/order_creation/transaction.html.twig', [
            'response' => $response
        ]);
    }
}
