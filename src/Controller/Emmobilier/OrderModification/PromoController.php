<?php

namespace App\Controller\Emmobilier\OrderModification;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Order;

use App\Form\Emmobilier\PromoCodeType;

/**
 * @Route("/promo/edit")
 *
 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class PromoController extends Controller
{
    use \App\Controller\Emmobilier\Traits\ControllerTrait;
	
	/**
	 * Render PromoCode Form.
	 *
	 * @param Order $order
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function renderPromoCodeForm(Order $order)
    {
        $codePromo = is_null($order->getPromotion()) ? null : $order->getPromotion()->getPromoCode();

        $form = $this->createForm(PromoCodeType::class, null, [
          'action' => $this->generateUrl('edit_code_promo', ['id' => $order->getId()])
        ]);

        if (!is_null($codePromo)) {
          $form->submit(['promoCode' => $codePromo]);
        }

        return $this->render('emmobilier/order_modification/_promo_code_form.html.twig', [
            'order' => $order,
            'form' => $form->createView()
        ]);
    }
	
	/**
	 * Validate promo code action.
	 *
	 * @Route("/promo/{id}", requirements={"id"="\d+"}, name="edit_code_promo", methods="POST")
	 *
	 * @param Request $request
	 * @param Order $order
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 * @throws \Doctrine\DBAL\ConnectionException
	 */
    public function submitPromoCode(Request $request, Order $order)
    {
        $form = $this->createForm(PromoCodeType::class, null, ['user' => $this->getUser()])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order->setPromotion($form->get('promoCode')->getData());
	
            $this->getOrderHandler()->updateOrderPrice($order);

            return $this->json(['success' => true,'msg' => $this->trans("orders.recap.promo.msg.success", ['%promo_code' => $order->getPromotion()->getPromoCode()])]);
        }


        return $this->json(['success' => false, 'msg' => $this->getErrorMessages($form)]);
    }
}
