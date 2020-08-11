<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ControllerTrait;
use Gedmo\Translatable\Entity\Translation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Retouch;
use App\Form\Admin\RetouchType;

/**
 * @Route("retouch")
 * @Security("is_granted('ROLE_RETOUCH_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class RetouchController extends Controller
{
    use ControllerTrait;
	
	/**
	 * @Route("/", name="admin_retouch_index", methods="GET")
	 *
	 * @param Request $request
	 * @return Response
	 * @throws \Exception
	 */
    public function index(Request $request): Response
    {
        $retouches = $this->dynamicPaginator([
          'search' => $request->query->get('search', null),
          'page' => $request->query->get('page', 1)
        ], Retouch::class);

        return $this->render("admin/retouch/index.html.twig",[
          "retouches" => $retouches
        ]);
    }
	
	/**
	 * @Route("/new", name="admin_retouch_new", methods="GET|POST")
	 *
	 * @param Request $request
	 * @return Response
	 */
    public function new(Request $request): Response
    {
        $retouch = new Retouch();
        $form = $this->createForm(RetouchType::class, $retouch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $translations = $em->getRepository(Translation::class);
            $translations->translate($retouch, 'title', 'en', $form->get('title_en')->getData());
            $translations->translate($retouch, 'description', 'en', $form->get('description_en')->getData());

            $embedLink = $this->handleYoutubeLinkEmbed($retouch->getHelpLink());
            if ($embedLink != NULL) {
                $retouch->setHelpLink($embedLink);
            }
            
            $em->persist($retouch);
            $em->flush();

            $this->addFlash('flash_msg_success', $this->trans('admin.retouch.flash.created', [], 'admin'));

            return $this->redirectToRoute('admin_retouch_index');
        }

        return $this->render('admin/retouch/new.html.twig', [
            'retouch' => $retouch,
            'form' => $form->createView(),
        ]);
    }
	
	/**
	 * @Route("/{id}", name="admin_retouch_show", methods="GET")
	 *
	 * @param Retouch $retouch
	 * @return Response
	 */
    public function show(Retouch $retouch): Response
    {
        $translations = $this->getDoctrine()
            ->getManager()
            ->getRepository(Translation::class)
            ->findTranslations($retouch)
        ;

        return $this->render('admin/retouch/show.html.twig', [
          'retouch' => $retouch,
          'translations' => current($translations)
        ]);
    }
	
	/**
	 * @Route("/{id}/edit", name="admin_retouch_edit", methods="GET|POST")
	 *
	 * @param Request $request
	 * @param Retouch $retouch
	 * @return Response
	 */
    public function edit(Request $request, Retouch $retouch): Response
    {
        $form = $this->createForm(RetouchType::class, $retouch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (strlen(strip_tags($form->get('description')->getData(), "")) <= 1000) {
                $em = $this->getDoctrine()->getManager();

                $translations = $em->getRepository(Translation::class);
                $translations->translate($retouch, 'title', 'en', $form->get('title_en')->getData());
                $translations->translate($retouch, 'description', 'en', $form->get('description_en')->getData());

                $embedLink = $this->handleYoutubeLinkEmbed($retouch->getHelpLink());
                if ($embedLink != NULL) {
                    $retouch->setHelpLink($embedLink);
                }
                
                $em->flush();

                $this->addFlash('flash_msg_success', $this->trans('admin.retouch.flash.updated', [], 'admin'));
            } else {
                return $this->render('admin/retouch/edit.html.twig', [
                    'retouch' => $retouch,
                    'form' => $form->createView(),
                    'invalidMax' => true,
                ]);
            }
        }

        return $this->render('admin/retouch/edit.html.twig', [
            'retouch' => $retouch,
            'form' => $form->createView(),
        ]);
    }
	
	/**
	 * @Route("/{id}", name="admin_retouch_delete", methods="POST|DELETE")
	 *
	 * @param Request $request
	 * @param Retouch $retouch
	 * @return Response
	 */
    public function delete(Request $request, Retouch $retouch): Response
    {
        if ($this->isCsrfTokenValid('delete'.$retouch->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($retouch);
            $em->flush();

            $this->addFlash('flash_msg_success', $this->trans('admin.retouch.flash.deleted', [], 'admin'));
        }

        return $this->redirectToRoute('admin_retouch_index');
    }

    public function handleYoutubeLinkEmbed ($rawLink) {
        parse_str( parse_url( $rawLink, PHP_URL_QUERY ), $my_array_of_vars );
        
        if (!empty($my_array_of_vars['v'])) {
            return 'https://www.youtube.com/embed/' . $my_array_of_vars['v'];
        }
        return NULL; 
    }
}
