<?php

namespace App\Controller\Emmobilier\OrderCreation;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;

use App\Form\Emmobilier\PromoCodeType;

/**
 * @Route("/new")
 *
 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class PromoController extends Controller
{
    use \App\Controller\Emmobilier\Traits\ControllerTrait;
	
	/**
	 * Render PromoCode Form.
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function renderPromoCodeForm(Request $request)
    {
        $codePromo = $request->getSession()->get('promo_code', null);

        $form = $this->createForm(PromoCodeType::class, null, [
          'action' => $this->generateUrl('code_promo_validation')
        ]);

        if (!is_null($codePromo)) {
          $form->submit(['promoCode' => $codePromo]);
        }

        return $this->render('emmobilier/order_creation/_promo_code_form.html.twig', [
            'form' => $form->createView()
        ]);
    }
	
	/**
	 * Validate promo code action.
	 *
	 * @Route("/promo", name="code_promo_validation", methods="POST")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
    public function savePromoCodeInTheSession(Request $request)
    {
        $session = $request->getSession();

        $form = $this->createForm(PromoCodeType::class, null, [
          'user' => $this->getUser()
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $promo = $form->get('promoCode')->getData();

            $session->set('promo_code', $promo->getPromoCode());
            $session->set('success_promo_code', $this->trans("orders.recap.promo.msg.success", ['%promo_code' => $promo->getPromoCode()]));

            return $this->json(['success' => true,'msg' => $session->get('success_promo_code')]);
        }

        $session->remove('promo_code');
        $session->remove('success_promo_code');

        return $this->json(['success' => false, 'msg' => $this->getErrorMessages($form)]);
    }
}
