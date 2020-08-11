<?php

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Handlers\FileHandler;
use App\Form\Admin\FileUploadType;
use App\Form\Admin\FileDeleteType;

/**
 * @Route("field/renovation/choices")
 * @Security("is_granted('ROLE_SUPER_ADMIN')")
 */
class FieldRenovationChoicesController extends Controller
{
	/**
	 * Upload action.
	 *
	 * @Route("/done/upload", name="admin_upload_renovation_choices", methods= "POST")
	 *
	 * @param Request $request
	 * @param FileHandler $uploader
	 * @return Response
	 */
    public function upload(Request $request, FileHandler $uploader): Response
    {
        $form = $this->createForm(FileUploadType::class)->handleRequest($request);
        $result = $uploader->handleFieldRenovationChoicesUpload($form->getData());

        return $this->json($result);
    }
	
	/**
	 * Delete action.
	 *
	 * @Route("/delete", name="admin_delete_renovation_choices", methods="POST")
	 *
	 * @param Request $request
	 * @param FileHandler $uploader
	 * @return Response
	 */
    public function delete(Request $request, FileHandler $uploader): Response
    {
        $form = $this->createForm(FileDeleteType::class)->handleRequest($request);
        $result = $uploader->removeFieldRenovationPicture($form->getData());

        return $this->json($result);
    }
}
