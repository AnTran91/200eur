<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ControllerTrait;
use App\Entity\FieldGroup;
use App\Form\Admin\FieldGroupType;
use App\Form\Admin\Filters\FieldGroupFilterType;
use App\Handlers\DynamicFormHandler;

use App\Handlers\FileHandler;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("field/group")
 * @Security("is_granted('ROLE_SUPER_ADMIN')")
 */
class FieldGroupController extends Controller
{
    use ControllerTrait;
	
	/**
	 * @Route("/", name="admin_field_group_index", methods="GET|POST")
	 *
	 * @param Request $request
	 * @param DynamicFormHandler $formFactory
	 * @return Response
	 */
    public function index(Request $request, DynamicFormHandler $formFactory): Response
    {
        $form = $this->createForm(FieldGroupFilterType::class)->handleRequest($request);

        $paramForm = null;
        $retouch = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $retouch = $form->get('retouch')->getData();
            /** @var FormInterface $paramForm */
            $paramForm = $formFactory->createParamViewForm($formFactory->getDefaultData($retouch), $retouch, $request->getLocale());
	        $paramForm->handleRequest($request);
        }

        return $this->render('admin/field_group/index.html.twig', [
          'form' => $form->createView(),
          'param_form' => is_null($paramForm) ? null : $paramForm->createView(),
          'retouch' => $retouch
        ]);
    }
	
	/**
	 * @Route("/new", name="admin_field_group_new", methods="GET|POST")
	 *
	 * @param Request $request
	 * @return Response
	 */
    public function new(Request $request): Response
    {
        $fieldGroup = new FieldGroup();
        $form = $this->createForm(FieldGroupType::class, $fieldGroup)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            /** @var \Gedmo\Translatable\Entity\Repository\TranslationRepository $translations */
            $translations = $em->getRepository('Gedmo\\Translatable\\Entity\\Translation');
            $translations->translate($fieldGroup, 'labelText', 'en', $fieldGroup->getLabelTextEn());

            foreach ($fieldGroup->getFields() as $field) {
                $translations->translate($field, 'labelText', 'en', $field->getLabelTextEn());

                foreach ($field->getChoices() as $choice) {
                    $translations->translate($choice, 'choiceLabel', 'en', $choice->getChoiceLabelEn());
                    $em->persist($choice);
                }

                foreach ($field->getRenovations() as $renovation) {
                    $translations->translate($renovation, 'typeName', 'en', $renovation->getTypeNameEn());
                    $em->persist($renovation);
                }

                $em->persist($field);
            }

            $em->persist($fieldGroup);
            $em->flush();

            $this->addFlash('flash_msg_success', $this->trans('admin.param_form.flash.created', [], 'admin'));
            return $this->redirectToRoute('admin_field_group_edit', ['id' => $fieldGroup->getId()]);
        }

        return $this->render('admin/field_group/new.html.twig', [
            'field_group' => $fieldGroup,
            'form' => $form->createView(),
        ]);
    }
	
	/**
	 * @Route("/{id}/edit", name="admin_field_group_edit", methods="GET|POST")
	 *
	 * @param Request $request
	 * @param FieldGroup $fieldGroup
	 * @return Response
	 */
    public function edit(Request $request, FieldGroup $fieldGroup): Response
    {
        $form = $this->createForm(FieldGroupType::class, $fieldGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            /** @var \Gedmo\Translatable\Entity\Repository\TranslationRepository $translations */
            $translations = $em->getRepository('Gedmo\\Translatable\\Entity\\Translation');

            $translations->translate($fieldGroup, 'labelText', 'en', $fieldGroup->getLabelTextEn());

            foreach ($fieldGroup->getFields() as $field) {
                $translations->translate($field, 'labelText', 'en', $field->getLabelTextEn());

                foreach ($field->getChoices() as $choice) {
                    $translations->translate($choice, 'choiceLabel', 'en', $choice->getChoiceLabelEn());
                }

                foreach ($field->getRenovations() as $renovation) {
                    $translations->translate($renovation, 'typeName', 'en', $renovation->getTypeNameEn());
                }
            }

            $em->flush();

            $this->addFlash('flash_msg_success', $this->trans('admin.param_form.flash.updated', [], 'admin'));
            return $this->redirectToRoute('admin_field_group_edit', ['id' => $fieldGroup->getId()]);
        }

        return $this->render('admin/field_group/edit.html.twig', [
            'field_group' => $fieldGroup,
            'form' => $form->createView(),
        ]);
    }
	
	/**
	 * @Route("/delete/{id}", name="admin_field_group_delete", methods="DELETE")
	 *
	 * @param Request $request
	 * @param FieldGroup $fieldGroup
	 * @param FileHandler $uploader
	 * @param EntityManagerInterface $em
	 *
	 * @return Response
	 */
    public function delete(Request $request, FieldGroup $fieldGroup, FileHandler $uploader, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fieldGroup->getId(), $request->request->get('_token'))) {
        	
            $em->beginTransaction();
            foreach ($fieldGroup->getFields() as $field) {
                foreach ($field->getChoices() as $choice) {
                    $em->remove($choice);
                }

                foreach ($field->getRenovations() as $renovation) {
                    foreach ($renovation->getFieldRenovationChoices() as $fieldRenovationChoice) {
                      $uploader->removeFieldRenovationPicture(['uuid' => $fieldRenovationChoice->getPictureDirectory()]);
                      $em->remove($fieldRenovationChoice);
                    }

                    $em->remove($renovation);
                }

                $em->remove($field);
            }

            $em->remove($fieldGroup);
            $em->flush();
            $em->commit();
        }

        $this->addFlash('flash_msg_success', $this->trans('admin.param_form.flash.deleted', [], 'admin'));
        return $this->redirectToRoute('admin_field_group_index');
    }
}
