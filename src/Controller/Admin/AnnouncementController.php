<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ControllerTrait;
use App\Entity\Announcement;
use App\Form\Admin\AnnouncementType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("announcement")
 * @Security("is_granted('ROLE_SUPER_ADMIN')")
 */
class AnnouncementController extends Controller
{
    use ControllerTrait;
	
	/**
	 * @Route("/", name="admin_announcement_check", methods="GET")
	 *
	 * @param Request $request
	 * @return Response
	 * @throws \Exception
	 */
    public function check(Request $request): Response
    {
        $announcement = $this->dynamicPaginator([
          'search' => $request->query->get('search', null),
          'page' => $request->query->get('page', 1)
        ], Announcement::class);

        if (count($announcement) == 0){
            return $this->redirectToRoute('admin_announcement_new');
        } else {
            return $this->redirectToRoute('admin_announcement_edit', ['id' => 1]);
        }
        
    }
	
	/**
	 * @Route("/new", name="admin_announcement_new", methods="GET|POST")
	 *
	 * @param Request $request
	 * @return Response
	 */
    public function new(Request $request): Response
    {
        $announcement = new Announcement();
        $form = $this->createForm(AnnouncementType::class, $announcement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($announcement);
            $em->flush();

            $this->addFlash('flash_msg_success', $this->trans('admin.announcement.flash.created', [], 'admin'));

            return $this->redirectToRoute('admin_announcement_check');
        }

        return $this->render('admin/announcement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
	
	// /**
	//  * @Route("/{id}", name="admin_announcement_show", methods="GET")
	//  *
	//  * @param Announcement $announcement
	//  * @return Response
	//  */
    // public function show(Announcement $announcement): Response
    // {
    //     return $this->render('admin/announcement/show.html.twig', ['announcement' => $announcement]);
    // }
	
	/**
	 * @Route("/{id}/edit", name="admin_announcement_edit", methods="GET|POST")
	 *
	 * @param Request $request
	 * @param Announcement $announcement
	 * @return Response
	 */
    public function edit(Request $request, Announcement $announcement): Response
    {
        $form = $this->createForm(AnnouncementType::class, $announcement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('flash_msg_success', $this->trans('admin.announcement.flash.updated', [], 'admin'));
        }

        return $this->render('admin/announcement/edit.html.twig', [
            'announcement' => $announcement,
            'form' => $form->createView(),
        ]);
    }

}
