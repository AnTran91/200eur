<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ControllerTrait;
use App\Entity\Holidays;
use App\Form\Admin\HolidaysType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("holidays")
 * @Security("is_granted('ROLE_HOLIDAYS_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class HolidaysController extends Controller
{
    use ControllerTrait;
	
	/**
	 * @Route("/", name="admin_holidays_index", methods="GET")
	 *
	 * @param Request $request
	 * @return Response
	 * @throws \Exception
	 */
    public function index(Request $request): Response
    {
        $holidays = $this->dynamicPaginator([
          'search' => $request->query->get('search', null),
          'page' => $request->query->get('page', 1)
        ], Holidays::class);

        return $this->render('admin/holidays/index.html.twig', [
          'holidays' => $holidays
        ]);
    }
	
	/**
	 * @Route("/new", name="admin_holidays_new", methods="GET|POST")
	 *
	 * @param Request $request
	 * @return Response
	 */
    public function new(Request $request): Response
    {
        $holiday = new Holidays();
        $form = $this->createForm(HolidaysType::class, $holiday);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($holiday);
            $em->flush();

            $this->addFlash('flash_msg_success', $this->trans('admin.holidays.flash.created', [], 'admin'));

            return $this->redirectToRoute('admin_holidays_index');
        }

        return $this->render('admin/holidays/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
	
	/**
	 * @Route("/{id}", name="admin_holidays_show", methods="GET")
	 *
	 * @param Holidays $holiday
	 * @return Response
	 */
    public function show(Holidays $holiday): Response
    {
        return $this->render('admin/holidays/show.html.twig', ['holiday' => $holiday]);
    }
	
	/**
	 * @Route("/{id}/edit", name="admin_holidays_edit", methods="GET|POST")
	 *
	 * @param Request $request
	 * @param Holidays $holiday
	 * @return Response
	 */
    public function edit(Request $request, Holidays $holiday): Response
    {
        $form = $this->createForm(HolidaysType::class, $holiday);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('flash_msg_success', $this->trans('admin.holidays.flash.updated', [], 'admin'));
        }

        return $this->render('admin/holidays/edit.html.twig', [
            'holiday' => $holiday,
            'form' => $form->createView(),
        ]);
    }
	
	/**
	 * @Route("/{id}", name="admin_holidays_delete", methods="DELETE")
	 *
	 * @param Request $request
	 * @param Holidays $holiday
	 * @return Response
	 */
    public function delete(Request $request, Holidays $holiday): Response
    {
        if ($this->isCsrfTokenValid('delete'.$holiday->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($holiday);
            $em->flush();

            $this->addFlash('flash_msg_success', $this->trans('admin.holidays.flash.deleted', [], 'admin'));
        }

        return $this->redirectToRoute('admin_holidays_index');
    }
}
