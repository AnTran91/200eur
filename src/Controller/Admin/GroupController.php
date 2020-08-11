<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Group;
use App\Form\Admin\GroupType;

/**
 * @Route("group")
 * @Security("is_granted('ROLE_USER_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class GroupController extends Controller
{
    use ControllerTrait;
	
	/**
	 * @Route("/", name="admin_group_index", methods="GET")
	 *
	 * @param Request $request
	 * @return Response
	 * @throws \Exception
	 */
    public function index(Request $request): Response
    {
        $groups = $this->dynamicPaginator([
          'search' => $request->query->get('search', null),
          'page' => $request->query->get('page', 1)
        ], Group::class);

        return $this->render('admin/group/index.html.twig', [
          "groups" => $groups,
        ]);
    }
	
	/**
	 * @Route("/new", name="admin_group_new", methods="GET|POST")
	 *
	 * @param Request $request
	 * @return Response
	 */
    public function new(Request $request): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();

            $this->addFlash('flash_msg_success', $this->trans('admin.group.flash.created', [], 'admin'));
            return $this->redirectToRoute('admin_group_index');
        }

        return $this->render('admin/group/new.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_group_show", methods="GET")
     */
    public function show(Group $group): Response
    {
        return $this->render('admin/group/show.html.twig', ['group' => $group]);
    }
	
	/**
	 * @Route("/{id}/edit", name="admin_group_edit", methods="GET|POST")
	 *
	 * @param Request $request
	 * @param Group $group
	 * @return Response
	 */
    public function edit(Request $request, Group $group): Response
    {
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('flash_msg_success', $this->trans('admin.group.flash.updated', [], 'admin'));
            return $this->redirectToRoute('admin_group_edit', ['id' => $group->getId()]);
        }

        return $this->render('admin/group/edit.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }
	
	/**
	 * @Route("/{id}", name="admin_group_delete", methods="DELETE")
	 *
	 * @param Request $request
	 * @param Group $group
	 * @return Response
	 */
    public function delete(Request $request, Group $group): Response
    {
        if ($this->isCsrfTokenValid('delete'.$group->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($group);
            $em->flush();
        }

        $this->addFlash('flash_msg_success', $this->trans('admin.group.flash.deleted', [], 'admin'));
        return $this->redirectToRoute('admin_group_index');
    }
}
