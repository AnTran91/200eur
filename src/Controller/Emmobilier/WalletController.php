<?php

namespace App\Controller\Emmobilier;

use App\Controller\Emmobilier\Traits\ControllerTrait;
use App\Controller\Emmobilier\Traits\SystemPayControllerTrait;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

use Symfony\Component\HttpFoundation\Request;

use App\Form\Emmobilier\WalletType;

/**
 * @Route("/wallet")
 *
 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class WalletController extends Controller
{
    use ControllerTrait;
    use SystemPayControllerTrait;
	
	/**
	 * New wallet transction Action
	 *
	 * @Route("/check", name="check_wallet_transaction", methods={"POST", "GET"})
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
    public function checkWalletTransaction(Request $request)
    {
        $form = $this->createForm(WalletType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->render('emmobilier/wallet/new.html.twig', [
              'amount' => $form->get('currentAmount')->getData(),
              'wallet_update_form' => $form->createView()
            ]);
        }

        $this->addFlash('flash_msg_success', implode("<br>", $this->getErrorMessages($form)));
        return $this->redirect($request->headers->get('referer', $this->generateUrl('order_list')));
    }
	
	/**
	 * Update wallet form
	 *
	 * @Route("/new", name="wallet_new", options = { "expose" = true }, methods="GET")
	 * @Cache(maxage="86400", smaxage="86400")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function renderWalletForm(Request $request)
    {
        $form = $this->createForm(WalletType::class, null, [
          'action' => $this->generateUrl('check_wallet_transaction'),
        ])->handleRequest($request);

        return $this->render('emmobilier/wallet/_modal.html.twig', [
          'form' => $form->createView()
        ]);
    }
}
