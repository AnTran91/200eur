<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ControllerTrait;
use App\Form\Admin\ProfilType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("profil")
 * @Security("is_granted('ROLE_SUPER_ADMIN') || is_granted('ROLE_AGENCY_MANAGER') || is_granted('ROLE_DELIVERY_TIME_MANAGER') || is_granted('ROLE_HOLIDAYS_MANAGER') ||
 is_granted('ROLE_INVOICE_MANAGER') || is_granted('ROLE_NETWORK_MANAGER') ||
 is_granted('ROLE_ORDER_MANAGER') || is_granted('ROLE_PRODUCTION_MANAGER') ||
 is_granted('ROLE_RETOUCH_MANAGER') || is_granted('ROLE_TRANSACTION_MANAGER') || is_granted('ROLE_USER_MANAGER')")
 */
class ProfileController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/", name="admin_profil_show", methods="GET")
     */
    public function show(): Response
    {
        return $this->render('admin/profil/show.html.twig');
    }
	
	/**
	 * @Route("/edit", name="admin_profil_edit", methods="GET|POST")
	 *
	 * @param Request $request
	 * @return Response
	 */
    public function edit(Request $request): Response
    {
        $form = $this->createForm(ProfilType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('user_modified', $this->trans('admin.user.user_modified', [], 'admin'));
            return $this->redirectToRoute('admin_profil_edit', ['id' => $this->getUser()->getId()]);
        }

        return $this->render('admin/profil/edit.html.twig', [
            'user' => $this->getUser(),
            'form' => $form->createView(),
        ]);
    }
}
