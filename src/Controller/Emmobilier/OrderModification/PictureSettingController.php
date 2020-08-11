<?php

namespace App\Controller\Emmobilier\OrderModification;

use App\Entity\PictureDetails;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

use Symfony\Component\HttpFoundation\Request;

use App\Form\Emmobilier\PictureRetouchListType;

use App\Entity\Retouch;
use App\Entity\Picture;

use App\Repository\RetouchRepository;

use App\Handlers\DynamicFormHandler;
use App\Handlers\ParamHandler;

/**
 * @Route("/edit/settings")
 *
 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class PictureSettingController extends Controller
{
    use \App\Controller\Emmobilier\Traits\ControllerTrait;
	
	/**
	 * Render picture details.
	 *
	 * @Route("/render/picture/{id}", requirements={"id"="\d+"}, name="edit_render_picture_detail", options = { "expose" = true }, methods="GET")
	 *
	 * @param Request $request
	 * @param Picture $picture
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function renderPictureSettings(Request $request, Picture $picture)
    {
        $retouchs = $picture->getPictureDetail()->map(function (PictureDetails $pictureDetail) {
            return $pictureDetail->getRetouch()->getId();
        })->toArray();

        $form = $this->createForm(PictureRetouchListType::class, null, [
            'locale' => $request->getLocale()
        ])->submit(["retouchs" => $retouchs]);

        return $this->render('emmobilier/order_modification/_form_settings.html.twig', [
            'id' => $request->query->get('id', 0),
            'picture' => $picture,
            'form' => $form->createView()
        ]);
    }
	
	/**
	 * Render retouch form.
	 *
	 * @Route("/render/retouch", requirements={"id"="\d+"}, name="edit_render_retouch_form", options = { "expose" = true }, methods={"GET", "POST"})
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
	 * Render form param.
	 *
	 * @Route("/render/params/{uuid}/{id}", requirements={"id"="\d+"}, name="edit_render_param_form", options = { "expose" = true }, methods={"GET", "POST"})
	 * @Entity("picture", expr="repository.find(uuid)")
	 *
	 * @param Request $request
	 * @param Retouch $retouch
	 * @param Picture $picture
	 * @param DynamicFormHandler $formFactory
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function renderParamForm(Request $request, Retouch $retouch, Picture $picture, DynamicFormHandler $formFactory)
    {
        $pictureDetail = $picture->getPictureDetail()->filter(function (PictureDetails $pictureDetail) use ($retouch) {
            return $retouch->getId() === $pictureDetail->getRetouch()->getId();
        })->first();

        $param = $pictureDetail ? $pictureDetail->getParam()->getElements() : $formFactory->getDefaultData($retouch);
        /** @var FormInterface $form */
        $form = $formFactory->createParamViewForm($param, $retouch, $request->getLocale());
	    $form->handleRequest($request);

        return $this->render('emmobilier/_shared_components/_param_form.html.twig', [
          'params' => $param,
          'form' => $form->createView()
        ]);
    }
	
	/**
	 * Render form param list.
	 *
	 * @Route("/param/list/form/{id}", requirements={"id"="\d+"}, name="edit_render_param_list_form", options = { "expose" = true }, methods={"GET", "POST"})
	 *
	 * @param Request $request
	 * @param Picture $picture
	 * @param RetouchRepository $retouchRepository
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function renderParamListForm(Request $request, Picture $picture, RetouchRepository $retouchRepository)
    {
        $form = $this->createForm(PictureRetouchListType::class)->handleRequest($request);
        $retouchs = $retouchRepository->findByArrayOfIdsAndLocale($form->get('retouchs')->getData(), $request->getLocale());

        return $this->render('emmobilier/_shared_components/_param_list.html.twig', [
          'retouchs' => $retouchs,
          'uuid' => $picture->getId()
        ]);
    }
	
	/**
	 * Add retouch to picture.
	 *
	 * @Route("/new/{id}", requirements={"id"="\d+"}, name="edit_picure_settings", methods="POST")
	 *
	 * @param Request $request
	 * @param Picture $picture
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
    public function setPictureSettings(Request $request, Picture $picture)
    {
        $form = $this->createForm(PictureRetouchListType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new \App\Events\ParamEvent([
              'request' => $request,
              'userDir' => $this->getUser()->getUserDirectory(),
              'picture' => $picture,
              'formData' => $form->getData()
            ]);
            $this->dispatch(\App\Utils\Events::ON_UPDATE_PARAMS, $event);

            if (count($event->getViolations()) > 0) {
                return $this->json(['success' => false, 'msg' => $event->getViolations()]);
            }
            return $this->json(['success' => true, 'msg' => $this->trans('uploader.msg.setting.success')]);
        }

        return $this->json(['success' => false, 'msg' => implode(', ', $this->getErrorMessages($form))]);
    }
}
