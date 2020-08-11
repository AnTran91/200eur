<?php

namespace App\Controller\Emmobilier\OrderCreation;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;

use App\Form\Emmobilier\PictureRetouchListType;

use App\Entity\Retouch;


use App\Repository\RetouchRepository;

use App\Handlers\DynamicFormHandler;
use App\Handlers\PictureHandler;

/**
 * @Route("/create/settings")
 *
 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class PictureSettingController extends Controller
{
    use \App\Controller\Emmobilier\Traits\ControllerTrait;

	/**
	 * Render picture details.
	 *
	 * @Route("/render/picture/{id}", requirements={"id"="[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}+"}, name="render_picture_detail", options = { "expose" = true }, methods="GET")
	 *
	 * @param Request $request
	 * @param string $id
	 * @param PictureHandler $pictureHandler
	 * @param RetouchRepository $retouchRepository
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function renderPictureSettings(Request $request, string $id, PictureHandler $pictureHandler, RetouchRepository $retouchRepository)
    {
        $picture = $pictureHandler->getTmpFile($this->getUser()->getUserDirectory(), $id);

        $form = $this->createForm(PictureRetouchListType::class, null, [
            'locale' => $request->getLocale()
        ])->submit(["retouchs" => $picture['retouch']]);

        return $this->render('emmobilier/order_creation/_form_settings.html.twig', [
            'id' => $request->query->get('id', 0),
            'retouchs' => $retouchRepository->findByArrayOfIdsAndLocale($picture['retouch'], $request->getLocale()),
            'picture' => $picture,
            'form' => $form->createView()
        ]);
    }

	/**
	 * Render retouch form.
	 *
	 * @Route("/render/retouch", name="render_retouch_form", options = { "expose" = true }, methods={"GET", "POST"})
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function renderRetouchForm(Request $request)
    {
        $form = $this->createForm(PictureRetouchListType::class, null, [
          'action' => $this->generateUrl('render_param_list_form'),
          'locale' => $request->getLocale()
        ])->handleRequest($request);

        return $this->render('emmobilier/_shared_components/_retouch_form.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Json form param.
     *
     * @Route("/json/params/{uuid}/{id}", requirements={"uuid"="[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}+"},  name="json_param_form", options = { "expose" = true }, methods={"GET", "POST"})
     *
     * @param Request $request
     * @param string $uuid
     * @param Retouch $retouch
     * @param DynamicFormHandler $formFactory
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function jsonParamForm(Request $request, string $uuid, Retouch $retouch, DynamicFormHandler $formFactory)
    {
        $session = $request->getSession();
        $params = $session->get('params', []);

        $param = $params[$uuid][$retouch->getId()] ?? $formFactory->getDefaultData($retouch);

        $params[$uuid][$retouch->getId()] = $param;
        $session->set('params', $params);
        return $this->json(['params' => $param]);
    }

	/**
	 * Render form param.
	 *
	 * @Route("/render/params/{uuid}/{id}", requirements={"uuid"="[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}+"},  name="render_param_form", options = { "expose" = true }, methods={"GET", "POST"})
	 *
	 * @param Request $request
	 * @param string $uuid
	 * @param Retouch $retouch
	 * @param DynamicFormHandler $formFactory
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function renderParamForm(Request $request, string $uuid, Retouch $retouch, DynamicFormHandler $formFactory)
    {
        $session = $request->getSession();
        $params = $session->get('params', []);

        $param = $params[$uuid][$retouch->getId()] ?? $formFactory->getDefaultData($retouch);
        /** @var FormInterface $form */
        $form = $formFactory->createParamViewForm($param, $retouch, $request->getLocale());
	    $form->handleRequest($request);

        $params[$uuid][$retouch->getId()] = $param;
        $session->set('params', $params);
        return $this->render('emmobilier/_shared_components/_param_form.html.twig', ['params' => $param, 'form' => $form->createView()]);
    }

	/**
	 * Render form param list.
	 *
	 * @Route("/param/list/form/{id}", requirements={"id"="[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}+"}, name="render_param_list_form", options = { "expose" = true }, methods={"GET", "POST"})
	 *
	 * @param Request $request
	 * @param string $id
	 * @param RetouchRepository $retouchRepository
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function renderParamListForm(Request $request, string $id, RetouchRepository $retouchRepository)
    {
        $form = $this->createForm(PictureRetouchListType::class)->handleRequest($request);

        $retouchs = $retouchRepository->findByArrayOfIdsAndLocale($form->get('retouchs')->getData(), $request->getLocale());

        return $this->render('emmobilier/_shared_components/_param_list.html.twig', ['retouchs' => $retouchs, 'uuid' => $id]);
    }

	/**
	 * Add retouch to picture.
	 *
	 * @Route("/new/{uuid}", requirements={"uuid"="[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}+"}, name="new_picure_settings", methods="POST")
	 *
	 * @param Request $request
	 * @param string $uuid
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
    public function setPictureSettings(Request $request, string $uuid)
    {
        $form = $this->createForm(PictureRetouchListType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new \App\Events\ParamEvent([
              'request' => $request,
              'userDir' => $this->getUser()->getUserDirectory(),
              'pictureDir' => $uuid,
              'formData' => $form->getData()
            ]);
            $this->dispatch(\App\Utils\Events::ON_SAVE_PARAMS, $event);

            if (count($event->getViolations()) > 0) {
              return $this->json(['success' => false, 'msg' => $event->getViolations()]);
            }
            return $this->json(['success' => true, 'msg' => $this->trans('uploader.msg.setting.success')]);
        }

        return $this->json(['success' => false, 'msg' => implode(', ', $this->getErrorMessages($form))]);
    }
}
