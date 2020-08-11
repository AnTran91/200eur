<?php

namespace App\Controller\Emmobilier;

use App\Controller\Emmobilier\Traits\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use App\Form\Emmobilier\BillingAddressType;

/**
 * @Route("/billing_address")
 * @Security("is_granted('ROLE_EMMOBILIER_USER')")
 */
class BillingAddressController extends Controller
{
    use ControllerTrait;
	
	/**
	 * Render Billing Address form.
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function renderForm(Request $request)
    {
        $billingAddress = $this->getUser()->getBillingAddress();

        $form = $this->createForm(BillingAddressType::class, $billingAddress, [
          'action' => $this->generateUrl('billing_address_edit')
        ])->handleRequest($request);

        return $this->render('emmobilier/billing_address/_modal.html.twig', [
          'form' => $form->createView()
        ]);
    }
	
	/**
	 * Edit User information (Billing Address) Action
	 *
	 * @Route("/edit", name="billing_address_edit", methods="POST")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
    public function edit(Request $request)
    {
        $billingAddress = $this->getUser()->getBillingAddress();

        $form = $this->createForm(BillingAddressType::class, $billingAddress)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->json(['success' => true, 'msg' => $this->trans('profile.flash.updated', array(), 'FOSUserBundle')]);
        }
        return $this->json(['success' => false, 'msg' => $this->getErrorMessages($form, 'user.field.%s')]);
    }
}
