<?php

namespace App\Controller\Emmobilier;

use App\Controller\Emmobilier\Traits\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use App\Entity\Picture;
use App\Entity\PictureDetails;

use App\Handlers\OrderHandler;

use App\Form\Emmobilier\RefusedPictureType;

/**
 * @Route("/picture")
 * @Security("is_granted('ROLE_EMMOBILIER_USER')")
 */
class PictureController extends Controller
{
    use ControllerTrait;
	
	/**
	 * @Route("/{id}/refuse", name="picture_refuse", requirements={"id"="\d+"}, options = { "expose" = true }, methods="POST")
	 * @Security("pictureDetails.isAwaitingForVerification()")
	 *
	 * @param Request           $request
	 * @param PictureDetails    $pictureDetails
	 * @param OrderHandler      $orderHandler
	 *
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 * @throws \Doctrine\DBAL\ConnectionException
	 */
    public function refuse(Request $request, PictureDetails $pictureDetails, OrderHandler $orderHandler)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $form = $this->createForm(RefusedPictureType::class, $pictureDetails->getReturnedPicture());
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if (null != $pictureDetails->getReturnedGifPicture()) {
                    $pictureDetails->getReturnedGifPicture()->setStatus(Picture::REFUSED);
                }
                $orderHandler->updateOrderStatus($pictureDetails->getPicture()->getOrder());
                $this->getDoctrine()->getManager()->flush();

                return $this->json([
                  'success' => true,
                  'msg' => $this->trans('orders.msg.refused'),
                  'orderDetail' => $this->renderView('emmobilier/_shared_components/_picture_verify.html.twig', ['orderDetail' => $pictureDetails]),
                  'order' => $this->renderView('emmobilier/_shared_components/_order_verify.html.twig', ['order' => $pictureDetails->getPicture()->getOrder()])
                ]);
            }
        }

        return $this->json(['msg' => $this->trans('orders.msg.error'), 'success' => false]);
    }
	
	/**
	 * @Route("/{id}/accept", name="picture_confirm", requirements={"id"="\d+"}, options = { "expose" = true }, methods="POST")
	 * @Security("pictureDetails.isAwaitingForVerification()")
	 *
	 * @param Request           $request
	 * @param PictureDetails    $pictureDetails
	 * @param OrderHandler      $orderHandler
	 *
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 * @throws \Doctrine\DBAL\ConnectionException
	 */
    public function accept(Request $request, PictureDetails $pictureDetails, OrderHandler $orderHandler)
    {
        $token = $request->request->get('_token');
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            if (!$this->isCsrfTokenValid('picture_confirm'.$pictureDetails->getReturnedPicture()->getId(), $token)) {
                return $this->json(['msg' => $this->trans('orders.msg.token_error'), 'success' => false]);
            }

            $pictureDetails->getReturnedPicture()->setStatus(Picture::VALIDATED);
            if (null != $pictureDetails->getReturnedGifPicture()) {
                $pictureDetails->getReturnedGifPicture()->setStatus(Picture::VALIDATED);
            }
            if (null != $pictureDetails->getReturnedMP4Picture()) {
                $pictureDetails->getReturnedMP4Picture()->setStatus(Picture::VALIDATED);
            }
            $orderHandler->updateOrderStatus($pictureDetails->getPicture()->getOrder());
            $this->getDoctrine()->getManager()->flush();

            return $this->json([
              'success' => true,
              'msg' => $this->trans('orders.msg.validated'),
              'orderDetail' => $this->renderView('emmobilier/_shared_components/_picture_verify.html.twig', ['orderDetail' => $pictureDetails]),
              'order' => $this->renderView('emmobilier/_shared_components/_order_verify.html.twig', ['order' => $pictureDetails->getPicture()->getOrder()])
            ]);
        }

        return $this->json(['msg' => $this->trans('orders.msg.error'), 'success' => false]);
    }

    public function renderRefuseForm()
    {
        $form = $this->createForm(RefusedPictureType::class);

        return $this->render('emmobilier/order/_refuse_modal.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
