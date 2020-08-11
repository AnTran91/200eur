<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ControllerTrait;
use App\Entity\Agency;
use App\Entity\User;

use App\Form\Admin\AgencyType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/agency")
 * @Security("is_granted('ROLE_AGENCY_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class AgencyController extends Controller
{
    use ControllerTrait;
	
	/**
	 * @Route("/", name="agency_index", methods="GET")
	 *
	 * @param Request $request
	 * @return Response
	 * @throws \Exception
	 */
    public function index(Request $request): Response
    {
        $agencies = $this->dynamicPaginator([
          'search' => $request->query->get('search', null),
          'page' => $request->query->get('page', 1)
        ], Agency::class);
        
        foreach ($agencies as $agency) {
            $agency->numberOfUser = count($this->getDoctrine()->getManager()->getRepository(User::class)->getQueryFindByOrganization($agency->getId())->getResult());
        }

        return $this->render('admin/agency/index.html.twig', [
          'agencies' => $agencies
        ]);
    }
	
	/**
	 * @Route("/new", name="agency_new", methods="GET|POST")
	 *
	 * @param Request $request
	 * @return Response
	 */
    public function new(Request $request): Response
    {
        $agency = new Agency();
        $form = $this->createForm(AgencyType::class, $agency);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // getting data from form
            $em = $this->getDoctrine()->getManager();
            $em->persist($agency);
            $em->flush();

            $this->addFlash('flash_msg_success', $this->trans('admin.agency.flash.created', [], 'admin'));

            return $this->redirectToRoute('agency_index');
        }

        return $this->render('admin/agency/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
	
	/**
	 * @Route("/{id}", name="agency_show", methods="GET")
	 *
	 * @param Request $request
	 * @param Agency $agency
	 * @return Response
	 */
    public function show(Request $request, Agency $agency): Response
    {
        $employees = $this->basicPaginator([
          'page' => $request->query->get('page', 1)
        ], $this->getDoctrine()->getManager()->getRepository(User::class)->getQueryFindByOrganization($agency->getId()));

        return $this->render('admin/agency/show.html.twig', [
          'agency' => $agency,
          'employees' => $employees
        ]);
    }
	
	/**
	 * @Route("/{id}/edit", name="agency_edit", methods="GET|POST")
	 *
	 * @param Request $request
	 * @param Agency $agency
	 * @return Response
	 */
    public function edit(Request $request, Agency $agency): Response
    {
        $form = $this->createForm(AgencyType::class, $agency);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('flash_msg_success', $this->trans('admin.agency.flash.updated', [], 'admin'));

            return $this->redirectToRoute('agency_edit', ['id' => $agency->getId()]);
        }

        return $this->render('admin/agency/edit.html.twig', [
            'agency' => $agency,
            'form' => $form->createView(),
        ]);
    }
	
	/**
	 * @Route("/{id}", name="agency_delete", methods="DELETE")
	 * 
	 * @param Request $request
	 * @param Agency $agency
	 * @return Response
	 */
    public function delete(Request $request, Agency $agency): Response
    {
        if ($this->isCsrfTokenValid('delete'.$agency->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($agency);
            $em->flush();

            $this->addFlash('flash_msg_success', $this->trans('admin.agency.flash.deleted', [], 'admin'));
        }

        return $this->redirectToRoute('agency_index');
    }
}
