<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Network;
use App\Entity\User;
use App\Form\Admin\NetworkType;

/**
 * @Route("network")
 * @Security("is_granted('ROLE_NETWORK_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class NetworkController extends Controller
{
    use ControllerTrait;
	
	/**
	 * @Route("/", name="network_index", methods="GET")
	 *
	 * @param Request $request
	 * @return Response
	 * @throws \Exception
	 */
    public function index(Request $request): Response
    {
        $networks = $this->dynamicPaginator([
          'search' => $request->query->get('search', null),
          'page' => $request->query->get('page', 1)
        ], Network::class);

        foreach ($networks as $network) {
            $network->numberOfUser = count($this->getDoctrine()->getManager()->getRepository(User::class)->getQueryFindByOrganization($network->getId())->getResult());
        }

        return $this->render('admin/network/index.html.twig', [
          'networks' => $networks
        ]);
    }
	
	/**
	 * @Route("/new", name="network_new", methods="GET|POST")
	 *
	 * @param Request $request
	 * @return Response
	 */
    public function new(Request $request): Response
    {
        $network = new Network();
        $form = $this->createForm(NetworkType::class, $network);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // getting data from form
            $em = $this->getDoctrine()->getManager();
            $em->persist($network);
            $em->flush();

            return $this->redirectToRoute('network_index');
        }

        return $this->render('admin/network/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
	
	/**
	 * @Route("/{id}", name="network_show", methods="GET")
	 *
	 * @param Request $request
	 * @param Network $network
	 * @param PaginatorInterface $paginator
	 * @return Response
	 */
    public function show(Request $request, Network $network, PaginatorInterface $paginator): Response
    {
        $employees = $this->basicPaginator([
          'page' => $request->query->get('page', 1)
        ], $this->getDoctrine()->getManager()->getRepository(User::class)->getQueryFindByOrganization($network->getId()));

        return $this->render('admin/network/show.html.twig', [
          'network' => $network,
          'employees' => $employees
        ]);
    }
	
	/**
	 * @Route("/{id}/edit", name="network_edit", methods="GET|POST")
	 *
	 * @param Request $request
	 * @param Network $network
	 * @return Response
	 */
    public function edit(Request $request, Network $network): Response
    {
        $form = $this->createForm(NetworkType::class, $network);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('admin/network/edit.html.twig', [
            'network' => $network,
            'form' => $form->createView(),
        ]);
    }
	
	/**
	 * @Route("/{id}", name="network_delete", methods="DELETE")
	 *
	 * @param Request $request
	 * @param Network $network
	 * @return Response
	 */
    public function delete(Request $request, Network $network): Response
    {
        if ($this->isCsrfTokenValid('delete'.$network->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($network);
            $em->flush();
        }

        return $this->redirectToRoute('network_index');
    }
}
