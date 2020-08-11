<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Production;
use App\Form\Admin\ProductionType;

/**
 * @Route("production")
 * @Security("is_granted('ROLE_PRODUCTION_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class ProductionController extends Controller
{
    use ControllerTrait;
	
	/**
	 * @Route("/", name="admin_production_index", methods="GET")
	 *
	 * @param Request $request
	 * @return Response
	 * @throws \Exception
	 */
    public function index(Request $request): Response
    {
        $productions = $this->dynamicPaginator([
          'search' => $request->query->get('search', null),
          'page' => $request->query->get('page', 1)
        ], Production::class);

        return $this->render('admin/production/index.html.twig', [
          'productions' => $productions
        ]);
    }
	
	/**
	 * @Route("/new", name="admin_production_new", methods="GET|POST")
	 *
	 * @param Request $request
	 * @return Response
	 */
    public function new(Request $request): Response
    {
        $production = new Production();
        $form = $this->createForm(ProductionType::class, $production);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($production);
            $em->flush();

            return $this->redirectToRoute('admin_production_index');
        }

        return $this->render('admin/production/new.html.twig', [
            'production' => $production,
            'form' => $form->createView(),
        ]);
    }
	
	/**
	 * @Route("/{id}", name="admin_production_show", methods="GET")
	 *
	 * @param Production $production
	 * @return Response
	 */
    public function show(Production $production): Response
    {
        return $this->render('admin/production/show.html.twig', ['production' => $production]);
    }
	
	/**
	 * @Route("/{id}/edit", name="admin_production_edit", methods="GET|POST")
	 *
	 * @param Request $request
	 * @param Production $production
	 * @return Response
	 */
    public function edit(Request $request, Production $production): Response
    {
        $form = $this->createForm(ProductionType::class, $production);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_production_edit', ['id' => $production->getId()]);
        }

        return $this->render('admin/production/edit.html.twig', [
            'production' => $production,
            'form' => $form->createView(),
        ]);
    }
	
	/**
	 * @Route("/{id}", name="admin_production_delete", methods="POST|DELETE")
	 *
	 * @param Request $request
	 * @param Production $production
	 * @return Response
	 */
    public function delete(Request $request, Production $production): Response
    {
        if ($this->isCsrfTokenValid('delete'.$production->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($production);
            $em->flush();
        }

        return $this->redirectToRoute('admin_production_index');
    }
}
