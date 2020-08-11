<?php
/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Emmobilier\OrderCreation;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use App\Handlers\PictureHandler;
use App\Form\Emmobilier\FileUploadType;

use App\Events\UploadEvent;
use App\Utils\Events;

/**
 * Fine Uploader Controller
 *
 * @Route("/tmp/uploader")
 * @Security("is_granted('ROLE_EMMOBILIER_USER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class FileUploaderController extends Controller
{
    use \App\Controller\Emmobilier\Traits\ControllerTrait;
	
	/**
	 * Index action.
	 *
	 * @Route("/files", name="fine_uploader_index", options = { "expose" = true }, methods= "GET")
	 *
	 * @param Request $request
	 * @param PictureHandler $pictureHandler
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
    public function index(Request $request, PictureHandler $pictureHandler)
    {
        $response = $pictureHandler->getFilesByCurrentLocale($this->getUser()->getUserDirectory(), $request->getLocale());

        return $this->json($response);
    }
	
	/**
	 * Upload action.
	 *
	 * @Route("/uploads", name="fine_uploader_upload", options = { "expose" = true }, methods= "POST")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
    public function upload(Request $request)
    {
        $form = $this->createForm(FileUploadType::class)->handleRequest($request);

        $event = new UploadEvent($request, $this->getUser()->getUserDirectory(), $form);
        $this->dispatch(Events::ON_UPLOAD, $event);

        return $this->json($event->getData(), $event->getStatusCode());
    }
	
	/**
	 * Chunking action.
	 *
	 * @Route("/chunking", name="fine_uploader_chunking", options = { "expose" = true }, methods= "POST")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
    public function chunking(Request $request)
    {
        $form = $this->createForm(FileUploadType::class)->handleRequest($request);

        $event = new UploadEvent($request, $this->getUser()->getUserDirectory(), $form);
        $this->dispatch(Events::ON_CHUNK_UPLOAD, $event);

        return $this->json($event->getData(), $event->getStatusCode());
    }

    /**
     * Delete action.
     *
     * @Route("/delete", name="fine_uploader_delete", options = { "expose" = true }, methods= "POST|DELETE")
     */
    public function delete(Request $request)
    {
        $event = new UploadEvent($request, $this->getUser()->getUserDirectory());
        $this->dispatch(Events::ON_DELETE_UPLOADED_FILE, $event);

        return $this->json($event->getData(), $event->getStatusCode());
    }
	
	/**
	 * Delete action.
	 *
	 * @Route("/multiple/delete", name="fine_uploader_multiple_delete", options = { "expose" = true }, methods= "POST|DELETE")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
    public function deleteMultiple(Request $request)
    {
        $event = new UploadEvent($request, $this->getUser()->getUserDirectory());
        $this->dispatch(Events::ON_DELETE_MULTIPLE_UPLOADED_FILE, $event);

        return $this->json($event->getData(), $event->getStatusCode());
    }
}
