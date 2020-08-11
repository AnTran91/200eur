<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ControllerTrait;
use App\Entity\Retouch;
use App\Entity\PictureDetails;

use App\Form\Admin\RefusedPictureType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Handlers\DynamicFormHandler;
use App\Handlers\ParamHandler;

use App\Handlers\FileHandler;
use App\Form\Admin\FileUploadType;
use App\Form\PictureRetouchListType;

use App\Events\ChunkedFileUploadEvent;
use App\Utils\Events;

/**
 * @Route("picture")
 * @Security("is_granted('ROLE_ORDER_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class PictureController extends Controller
{
    use ControllerTrait;
	
	/**
	 * Upload action.
	 *
	 * @Route("/done/upload/{id}", name="admin_upload_done_img", methods= "POST")
	 *
	 * @param Request $request
	 * @param PictureDetails $pictureDetail
	 * @return Response
	 */
    public function uploadDonePicture(Request $request, PictureDetails $pictureDetail): Response
    {
        $form = $this->createForm(FileUploadType::class)->handleRequest($request);

        $event = new ChunkedFileUploadEvent($request, $pictureDetail, $form);
        $this->dispatch(Events::ON_UPLOAD_FINISHED_PICTURE, $event);

        return $this->json($event->getData(), $event->getStatusCode());
    }
	
	/**
	 * Delete action.
	 *
	 * @Route("/delete/{id}", name="admin_delete_done_img", methods= "POST")
	 *
	 * @param PictureDetails $pictureDetail
	 * @param FileHandler $uploader
	 * @return Response
	 */
    public function deleteDonePicture(PictureDetails $pictureDetail, FileHandler $uploader): Response
    {
        if ($pictureDetail->getReturnedPicture()) {
            $result = $uploader->removeReturnedPicture($pictureDetail->getPicture()->getOrder()->getClient()->getUserDirectory(), $pictureDetail->getPicture()->getOrder()->getUploadFolder(), $pictureDetail->getPicture()->getPictureDirectory(), $pictureDetail->getReturnedPicture()->getPictureDirectory());

            $em = $this->getDoctrine()->getManager();
            $em->remove($pictureDetail->getReturnedPicture());
            $pictureDetail->setReturnedPicture(null);
            $em->flush();

            return $this->json($result);
        }

        return $this->json(['success' => false, 'error' => $this->trans('uploader.msg.setting.error', [], 'admin')]);
    }

    /**
     * Upload gif action.
     *
     * @Route("/gif/upload/{id}", name="admin_upload_gif_img", methods= "POST")
     *
     * @param Request $request
     * @param PictureDetails $pictureDetail
     * @return Response
     */
    public function uploadGifPicture(Request $request, PictureDetails $pictureDetail): Response
    {
        $form = $this->createForm(FileUploadType::class)->handleRequest($request);

        $event = new ChunkedFileUploadEvent($request, $pictureDetail, $form);
        $this->dispatch(Events::ON_UPLOAD_GIF_PICTURE, $event);

        return $this->json($event->getData(), $event->getStatusCode());
    }

    /**
     * Delete action.
     *
     * @Route("/gif/delete/{id}", name="admin_delete_gif_img", methods= "POST")
     *
     * @param PictureDetails $pictureDetail
     * @param FileHandler $uploader
     * @return Response
     */
    public function deleteGifPicture(PictureDetails $pictureDetail, FileHandler $uploader): Response
    {
        if ($pictureDetail->getReturnedGifPicture()) {
            $result = $uploader->removeReturnedPicture($pictureDetail->getPicture()->getOrder()->getClient()->getUserDirectory(), $pictureDetail->getPicture()->getOrder()->getUploadFolder(), $pictureDetail->getPicture()->getPictureDirectory(), $pictureDetail->getReturnedGifPicture()->getPictureDirectory());

            $em = $this->getDoctrine()->getManager();
            $em->remove($pictureDetail->getReturnedGifPicture());
            $pictureDetail->setReturnedGifPicture(null);
            $em->flush();

            return $this->json($result);
        }

        return $this->json(['success' => false, 'error' => $this->trans('uploader.msg.setting.error', [], 'admin')]);
    }

    /**
     * Upload MP4 action.
     *
     * @Route("/mp4/upload/{id}", name="admin_upload_mp4_file", methods= "POST")
     *
     * @param Request $request
     * @param PictureDetails $pictureDetail
     * @return Response
     */
    public function uploadMP4File(Request $request, PictureDetails $pictureDetail): Response
    {
        $form = $this->createForm(FileUploadType::class)->handleRequest($request);

        $event = new ChunkedFileUploadEvent($request, $pictureDetail, $form);
        $this->dispatch(Events::ON_UPLOAD_MP4_FILE, $event);

        return $this->json($event->getData(), $event->getStatusCode());
    }

    /**
     * Delete action.
     *
     * @Route("/mp4/delete/{id}", name="admin_delete_mp4_file", methods= "POST")
     *
     * @param PictureDetails $pictureDetail
     * @param FileHandler $uploader
     * @return Response
     */
    public function deleteMP4Picture(PictureDetails $pictureDetail, FileHandler $uploader): Response
    {
      if ($pictureDetail->getReturnedGifPicture()) {
          $result = $uploader->removeReturnedPicture($pictureDetail->getPicture()->getOrder()->getClient()->getUserDirectory(), $pictureDetail->getPicture()->getOrder()->getUploadFolder(), $pictureDetail->getPicture()->getPictureDirectory(), $pictureDetail->getReturnedMP4Picture()->getPictureDirectory());

          $em = $this->getDoctrine()->getManager();
          $em->remove($pictureDetail->getReturnedMP4Picture());
          $pictureDetail->setReturnedMP4Picture(null);
          $em->flush();

          return $this->json($result);
      }

      return $this->json(['success' => false, 'error' => $this->trans('uploader.msg.setting.error', [], 'admin')]);
    }
	
	/**
	 * Render form param
	 *
	 * @Route("/render/param/{id_picture_detail}/retouch/{id_retouch}", name="admin_render_param_form", options = { "expose" = true }, methods={"GET", "POST"})
	 *
	 * @Entity("pictureDetail", expr="repository.find(id_picture_detail)")
	 * @Entity("retouch", expr="repository.find(id_retouch)")
	 * @param Request $request
	 * @param PictureDetails $pictureDetail
	 * @param Retouch $retouch
	 * @param DynamicFormHandler $formFactory
	 * @return Response
	 */
    public function renderPictureParamForm(Request $request, PictureDetails $pictureDetail, Retouch $retouch, DynamicFormHandler $formFactory)
    {
    	/** @var FormInterface $form */
        $form = $formFactory->createParamViewForm($pictureDetail->getParam()->getElements(), $retouch, $request->getLocale());
	    $form->handleRequest($request);

        return $this->render('admin/_shared_components/_param_form.html.twig', [
          'retouch' => $retouch,
          'pictureDetail' => $pictureDetail,
          'show_button' => true,
          'params' => $pictureDetail->getParam()->getElements(),
          'form' => $form->createView()
        ]);
    }
	
	/**
	 * Render form param
	 *
	 * @Route("/show/{id_picture_detail}/param", name="admin_view_param_form", options = { "expose" = true }, methods={"GET", "POST"})
	 * @Entity("pictureDetail", expr="repository.find(id_picture_detail)")
	 *
	 * @param Request $request
	 * @param PictureDetails $pictureDetail
	 * @param DynamicFormHandler $formFactory
	 * @return Response
	 */
    public function showPictureParamForm(Request $request, PictureDetails $pictureDetail, DynamicFormHandler $formFactory)
    {
        $form = $formFactory->createParamViewForAdmin($pictureDetail->getParam()->getElements(), $pictureDetail->getRetouch(), $request->getLocale())
                            ->handleRequest($request);

        return $this->render('admin/_shared_components/_param_form.html.twig', [
          'params' => $pictureDetail->getParam()->getElements(),
          'retouch' => $pictureDetail->getRetouch(),
          'pictureDetail' => $pictureDetail,
          'form' => $form->createView()
        ]);
    }
	
	/**
	 * @Route("/paint/{id}", name="admin_picture_paint", methods="GET")
	 *
	 * @param PictureDetails $pictureDetail
	 * @return Response
	 */
    public function paint(PictureDetails $pictureDetail): Response
    {
        return $this->render('admin/order/paint.html.twig', [
          'pictureDetail' => $pictureDetail
        ]);
    }
	
	/**
	 * Upload action.
	 *
	 * @Route("/painted/upload/{id}", name="admin_painted_picture_upload", methods="POST")
	 *
	 * @param Request $request
	 * @param PictureDetails $pictureDetail
	 * @return Response
	 */
    public function uploadPaintedPicture(Request $request, PictureDetails $pictureDetail): Response
    {
        $form = $this->createForm(FileUploadType::class)->handleRequest($request);

        $event = new ChunkedFileUploadEvent($request, $pictureDetail, $form);
        $this->dispatch(Events::ON_UPLOAD_PAINTED_PICTURE, $event);

        return $this->json($event->getData(), $event->getStatusCode());
    }
	
	/**
	 * Add retouch to picture.
	 *
	 * @Route("/edit/param/{id_picture_detail}/retouch/{id_retouch}", name="admin_edit_picure_param", methods="POST")
	 *
	 * @Entity("pictureDetail", expr="repository.find(id_picture_detail)")
	 * @Entity("retouch", expr="repository.find(id_retouch)")
	 *
	 * @param Request $request
	 * @param PictureDetails $pictureDetail
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
    public function editPictureParam(Request $request, PictureDetails $pictureDetail, Retouch $retouch)
    {

        $event = new \App\Events\ParamEvent([
          'request' => $request,
          'pictureDetail' => $pictureDetail,
          'retouch' => $retouch
        ]);
        $this->dispatch(Events::ON_UPDATE_ONE_PARAM, $event);

        if (count($event->getViolations()) == 0) {

          return $this->json(['success' => true, 'msg' => $this->trans('admin.picture.msg.update_setting', [], 'admin')]);
        }

        return $this->json(['success' => false, 'msg' => $event->getViolations()]);
    }

  /**
   *
   * @Route("/paint/{id_picture_detail}/delete", name="admin_delete_paint_picure", methods="GET|POST")
   *
   * @Entity("pictureDetail", expr="repository.find(id_picture_detail)")
   *
   * @param Request $request
   * @param PictureDetails $pictureDetail
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
    public function deletePaintedPicture(Request $request, PictureDetails $pictureDetail) {
      $entityManager = $this->getDoctrine()->getManager();
      $returnedPicture = $pictureDetail->getReturnedPicture();
      $returnedPicture->setPaintedPicturePathThumb(NULL);
      $returnedPicture->setPaintedPicturePath(NULL);
      $entityManager->persist($returnedPicture);
      $entityManager->flush();
      return $this->redirect($request->headers->get('referer'));
    }

    // /**
    //  * @Route("/delete/{id}", name="admin_picure_delete", methods="DELETE")
    //  */
    // public function delete(Request $request, PictureDetails $pictureDetail, FileHandler $uploader): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$pictureDetail->getId(), $request->request->get('_token'))) {
    //         $em = $this->getDoctrine()->getManager();
    //
    //         if ($pictureDetail->getReturnedPicture()) {
    //           $uploader->removeReturnedPicture($pictureDetail->getPicture()->getOrder()->getClient()->getUserDirectory(), $pictureDetail->getPicture()->getOrder()->getUploadFolder(), $pictureDetail->getPicture()->getPictureDirectory(), $pictureDetail->getReturnedPicture()->getPictureDirectory());
    //           $em->remove($pictureDetail->getReturnedPicture());
    //         }
    //
    //         if ($pictureDetail->getParam()) {
    //           $em->remove($pictureDetail->getParam());
    //         }
    //
    //         $em->remove($pictureDetail);
    //         $em->flush();
    //
    //         return $this->json(['success' => true, 'msg' => $this->trans('admin.picture.msg.edit', [], 'admin')]);
    //     }
    //
    //     return $this->json(['success' => false, 'msg' => $this->trans('uploader.msg.setting.error', [], 'admin')]);
    // }
}
