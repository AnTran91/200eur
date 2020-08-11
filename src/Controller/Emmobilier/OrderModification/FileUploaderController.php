<?php
/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Emmobilier\OrderModification;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use App\Handlers\PictureHandler;
use App\Form\Emmobilier\FileUploadType;

use App\Events\UploadEvent;
use App\Utils\Events;

use App\Entity\Order;

/**
 * Fine Uploader Controller
 *
 * @Route("/saved/uploader")
 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class FileUploaderController extends Controller
{
    use \App\Controller\Emmobilier\Traits\ControllerTrait;
	
	/**
	 * Index action.
	 *
	 * @Route("/files/{id}", name="edit_fine_uploader_index", requirements={"id"="\d+"}, options = {"expose" = true}, methods= "GET")
	 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN') && order.isPayed()")
	 *
	 * @param Order $order
	 * @param PictureHandler $pictureHandler
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
    public function index(Order $order, PictureHandler $pictureHandler)
    {
        $response = $pictureHandler->getFormattedPictures($order->getPictures());
        return $this->json($response);
    }
	
	/**
	 * Upload action.
	 *
	 * @Route("/uploads/{id}", name="edit_fine_uploader_upload", requirements={"id"="\d+"}, options = { "expose" = true }, methods= "POST")
	 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN') && order.isPayed()")
	 *
	 * @param Request $request
	 * @param Order $order
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
    public function upload(Request $request, Order $order)
    {
        $form = $this->createForm(FileUploadType::class)->handleRequest($request);

        $event = new UploadEvent($request, $this->getUser()->getUserDirectory(), $form, $order);
        $this->dispatch(Events::ON_UPLOAD, $event);

        return $this->json($event->getData(), $event->getStatusCode());
    }
	
	/**
	 * Chunking action.
	 *
	 * @Route("/chunking/{id}", name="edit_fine_uploader_chunking", requirements={"id"="\d+"}, options = { "expose" = true }, methods= "POST")
	 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN') && order.isPayed()")
	 *
	 * @param Request $request
	 * @param Order $order
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
    public function chunking(Request $request, Order $order)
    {
        $form = $this->createForm(FileUploadType::class)->handleRequest($request);

        $event = new UploadEvent($request, $this->getUser()->getUserDirectory(), $form, $order);
        $this->dispatch(Events::ON_CHUNK_UPLOAD, $event);

        return $this->json($event->getData(), $event->getStatusCode());
    }
	
	/**
	 * Delete action.
	 *
	 * @Route("/delete/{id}", name="edit_fine_uploader_delete", requirements={"id"="\d+"}, options = { "expose" = true }, methods= "POST|DELETE")
	 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN') && order.isPayed()")
	 *
	 * @param Request $request
	 * @param Order $order
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
    public function delete(Request $request, Order $order)
    {
        $event = new UploadEvent($request, $this->getUser()->getUserDirectory(), null, $order);
        $this->dispatch(Events::ON_DELETE_SAVED_FILE, $event);

        return $this->json($event->getData(), $event->getStatusCode());
    }
}
