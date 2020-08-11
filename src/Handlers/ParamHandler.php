<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers;

use App\Entity\Retouch;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class ParamHandler
{
    /**
     * @var FileHandler
     */
    private $uploader;

    /**
     * @var DynamicFormHandler
     */
    private $formFactory;

    /**
     * Constructor
     *
     * @param DynamicFormHandler          $formFactory
     * @param FileHandler         $uploader
     */
    public function __construct(DynamicFormHandler $formFactory, FileHandler $uploader)
    {
        $this->uploader = $uploader;
        $this->formFactory = $formFactory;
    }

    /**
     * Save params in the form
     *
     * @param array $data
     * @param array $oldParam
     * @param array $folderDestination
     *
     * @return array
     */
    public function handleOneParam(array $data, array $oldParam, ?array $folderDestination): array
    {
        $currentParam = [];
        foreach ($data as $key => $value) {
            if ($value instanceof UploadedFile) {
                if (isset($oldParam[$key]) && is_string($oldParam[$key]) && $this->uploader->isFile($oldParam[$key])) {
                    $this->uploader->removeFileOrDirectory($oldParam[$key]);
                }
                $currentParam[$key] = $this->uploader->uploadParamFile($value, $key, $folderDestination['userDir'], $folderDestination['pictureDir'], $folderDestination['orderUploadFolder']);
                continue;
            }

            if (is_null($value)){
                unset($data[$key]);
            }
        }

        return array_replace($oldParam, $data, $currentParam);
    }

    /**
     * This function handle upload and validation then return the new params with erros
     *
     * @param Request   $request
     * @param array     $oldParam
     * @param array     $retouchObjects
     * @param array     $options
     *
     * @return array
     */
    public function handleParams(Request $request, array $oldParam, array $retouchObjects, array $options): array
    {
        $currentParam = [];
        $errors = [];
        // validate param form
        foreach ($retouchObjects as $retouch) {
            $param = array_replace($this->formFactory->getDefaultData($retouch), $oldParam[$retouch->getId()] ?? []);

            $form = $this->formFactory->createParamFormWithConstraint($param, $retouch);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $currentParam[$retouch->getId()] = $this->handleOneParam($form->getData(), $param, $options);
            } else {
                $currentParam[$retouch->getId()] = $param;
            }

            if ($form->isSubmitted() && !$form->isValid()) {
                $errors[$retouch->getTitle()] = \App\Utils\Tools::getErrorMessagesWithLabels($form);
            }

            // Renovation fix.
            $requestParams = $request->request->get($retouch->getId());
            if ($requestParams) {
                if (!empty($requestParams['field_renovation'])) {
                    $currentParam[$retouch->getId()]['field_renovation'] = $requestParams['field_renovation'];
                }
            }
        }

        return ['errors' => $errors, 'validParams' => $currentParam];
    }

    /**
     * This function handle upload and validation then return the new params with erros
     *
     * @param Request   $request
     * @param array     $oldParam
     * @param array     $retouchObjects
     * @param array     $options
     *
     * @return array
     */
    public function handleParamsUpdate(Request $request, array $oldParam, array $retouchObjects, array $options): array
    {
        $currentParam = [];
        $errors = [];
        $oldRetouch = $request->attributes->get('pictureDetail')->getRetouch();
        // validate param form
        foreach ($retouchObjects as $retouch) {
            $param = array_replace($this->formFactory->getDefaultData($retouch), $oldParam[$oldRetouch->getId()] ?? []);

            $form = $this->formFactory->createParamFormWithConstraint($param, $retouch);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $currentParam[$retouch->getId()] = $this->handleOneParam($form->getData(), $param, $options);
            } else {
                $currentParam[$retouch->getId()] = $param;
            }

            if ($form->isSubmitted() && !$form->isValid()) {
                $errors[$retouch->getTitle()] = \App\Utils\Tools::getErrorMessagesWithLabels($form);
            }

            $defaultValue = $this->formFactory->getDefaultData($retouch);
            foreach (array_diff_key($currentParam[$retouch->getId()],$this->formFactory->getDefaultData($retouch)) as $key => $value) {
                unset($currentParam[$retouch->getId()][$key]);
            }
            // Renovation fix.
            $requestParams = $request->request->get($retouch->getId());
            if ($requestParams) {
                if (!empty($requestParams['field_renovation'])) {
                    $currentParam[$retouch->getId()]['field_renovation'] = $requestParams['field_renovation'];
                }
            }
        }

        return ['errors' => $errors, 'validParams' => $currentParam];
    }

    /**
     * This function handle upload and validation then return the new params with errors
     *
     * @param array $data
     *
     * @return array
     */
    public function handleImmosquareParams(array $data): array
    {
        $errors = [];
        $currentParam = [];
        // validate param form
        foreach ($data['images'] as $imageKey => $image) {
            foreach ($image['services'] as $serviceKey => $service){
            	/** @var Retouch $retouch */
            	$retouch = $service['service'];
                $param = array_replace_recursive($this->formFactory->getDefaultData($retouch), $service['settings'] ?? []);

                $form = $this->formFactory->createParamFormWithConstraint($param,$retouch);
                $form->submit($param);

                $currentParam['images'][$imageKey]['services'][$serviceKey]['settings'] = $param;
                if ($form->isSubmitted() && $form->isValid()) {
                    $currentParam['images'][$imageKey]['services'][$serviceKey]['settings'] = $this->handleOneParam($form->getData(), $param, null);
                }

                if (!$form->isValid()) {
                    $errors[$retouch->getTitle()] = \App\Utils\Tools::getFromErrorMessages($form);
                }
            }
        }

        return ['errors' => $errors, 'data' => $currentParam];
    }
}
