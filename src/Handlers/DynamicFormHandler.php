<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;

use App\Form\Shared\Type\BlockTitleType;

use App\Repository\FieldRepository;
use App\Repository\FieldGroupRepository;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use App\Entity\Field;
use App\Entity\Retouch;

/**
 * Dynamic FormHandler class will use to handle dynamic form.
 */
class DynamicFormHandler
{
    /**
     * @var array
     */
    private $dynamicFormTypeOptions;
    private $dynamicFormConstraints;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FieldRepository
     */
    private $fieldRepository;

    /**
     * @var FieldGroupRepository
     */
    private $fieldGroupRepository;

    /**
     * Constructor
     *
     * @param ValidatorInterface $validator
     * @param FormFactoryInterface $formFactory
     * @param FieldRepository $fieldRepository
     * @param FieldGroupRepository $fieldGroupRepository
     * @param array $dynamicFormTypeOptions
     * @param array $dynamicFormConstraints
     * @param string $defaultLocale
     */
    public function __construct(ValidatorInterface $validator, FormFactoryInterface $formFactory, FieldRepository $fieldRepository, FieldGroupRepository $fieldGroupRepository, array $dynamicFormTypeOptions, array $dynamicFormConstraints, string $defaultLocale)
    {
        $this->fieldRepository = $fieldRepository;
        $this->fieldGroupRepository = $fieldGroupRepository;
        $this->validator = $validator;

        $this->formFactory = $formFactory;
        // dynamic fields types parameters
        $this->dynamicFormTypeOptions = $dynamicFormTypeOptions;
        $this->dynamicFormConstraints = $dynamicFormConstraints;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * Creates and returns a form builder instance.
     *
     * @param string $name
     * @param array|null $data
     * @param array $options
     *
     * @return FormBuilderInterface
     */
    protected function createFormBuilder(string $name, $data = null, array $options = array()): FormBuilderInterface
    {
        return $this->formFactory->createNamedBuilder($name, FormType::class, $data, $options);
    }

    /**
     * Creates a dynamic param form
     *
     * @param array         $data       The data
     * @param Retouch|null  $retouch
     * @param string        $locale     The current locale
     *
     * @return string
     */
    public function createParamViewForm(array $data = null, ?Retouch $retouch = null, ?string $locale = null)
    {
        $form = $this->createFormBuilder($retouch->getId(), $data, [
          'csrf_protection' => false
        ]);

        $locale = is_null($locale) ? $this->defaultLocale : $locale;
        foreach ($this->getDefaultFields($locale, $retouch) as $key => $fieldGroup) {
            $form->add($key, BlockTitleType::class, [
              'label' => $fieldGroup->getLabelText(),
              'mapped' => false,
              'data' => $fieldGroup->getId(),
              'attr' => ['position' => $fieldGroup->getPosition(), 'rowclass' => 'lt-px-auto-small'],
              'translation_domain' => false
            ]);

            $fields = new \ArrayObject($fieldGroup->getFields()->toArray());
            $fieldsIterator = $fields->getIterator();
            while ($fieldsIterator->valid()) {
                $field = $fieldsIterator->current();
                $fieldsIterator->next();
                $nextField = $fieldsIterator->current();
                $form->add($field->getName(), $this->guessType($field), $this->getViewOptions($field, $nextField, $data));
            }
        }

        return $form->getForm();
    }

    /**
     * Creates a dynamic param form
     *
     * @param array $data The data
     * @param Retouch|null $retouch
     * @param string $locale locale
     *
     * @return FormInterface
     */
    public function createParamViewForAdmin(array $data, ?Retouch $retouch, ?string $locale): FormInterface
    {
        $form = $this->createFormBuilder($retouch->getId(), $data, [
          'csrf_protection' => false
        ]);

        if (!empty($data['field_renovation'])) {
            $renovation = $data['field_renovation'];
            $data['field_renovation'] = null;
        }

        $currentData = array_diff($data, $this->getDefaultData($retouch));
        if (isset($renovation)) {
            $currentData['field_renovation'] = $renovation;
        }

        $locale = is_null($locale) ? $this->defaultLocale : $locale;
        foreach ($this->getDefaultFields($locale, $retouch) as $key => $fieldGroup) {
            if ($fieldGroup->getFields()->map(function (Field $field) use ($currentData){
              return in_array($field->getName(), array_keys($currentData));})->isEmpty()) {
              $form->add($key, BlockTitleType::class, [
                'label' => $fieldGroup->getLabelText(),
                'mapped' => false,
                'data' => $fieldGroup->getId(),
                'attr' => ['position' => $fieldGroup->getPosition(), 'rowclass' => 'lt-px-auto-small'],
                'translation_domain' => false
              ]);
            }

            /** @var Field $field */
	        foreach ($fieldGroup->getFields() as $field) {
                if (!in_array($field->getName(), array_keys($currentData))) {
                    continue;
                }
                $form->add($field->getName(), $this->guessType($field), $this->getViewOptions($field, null, $data));
            }
        }

        return $form->getForm();
    }

    /**
     * Creates a dynamic param form
     *
     * @param array $data The data
     * @param Retouch|null $retouch
     *
     * @return FormInterface
     */
    public function createParamFormWithConstraint(array $data = null, ?Retouch $retouch = null): FormInterface
    {
        $form = $this->createFormBuilder($retouch->getId(), $data, [
          'csrf_protection' => false,
          'allow_extra_fields' => true
        ]);

        foreach ($this->getDefaultFields($this->defaultLocale, $retouch) as $fieldGroup) {
        	/** @var Field $field */
	        foreach ($fieldGroup->getFields() as $field) {
                $form->add($field->getName(), $this->guessType($field), $this->getConstraintsOptions($field));
            }
        }

        return $form->getForm();
    }

    /**
     * Get the form type class
     *
     * @param Field $field  The current field
     *
     * @return string  the form type name
     */
    public function guessType(Field $field): string
    {
        return $this->dynamicFormTypeOptions['types'][$field->getFieldType()];
    }

	/**
	 * Set a dynamic field options grouped by the order number
	 *
	 * @param Field $field The current field
	 * @param null|Field $nextField The next field
	 * @param array $data
	 * @return array
	 */
    public function getViewOptions(Field $field, ?Field $nextField, array $data): array
    {
        static $endblock = false;

        $options = [
          'label' => empty($field->getLabelText()) ? false : sprintf('%s %s', $field->getLabelText(), is_null($field->getPrice()) ? '' : sprintf('%s €', $field->getPrice())),
          'attr' => ['position' => $field->getFieldGroup()->getPosition()],
          'mapped' => $field->getMapped(),
          'translation_domain' => false
        ];

        if (!is_null($field->getDisabledOn()) && isset($data[$field->getDisabledOn()->getName()]) && $field->getDisabled() && $data[$field->getDisabledOn()->getName()] === $field->getDisabledOn()->getEmptyData()) {
            $options['disabled'] = $field->getDisabled();
        }

        $options['attr']['rowclass'] = $field->getHTMLClass();


        if ($field->getDisabled()) {
            $options['attr']['data-dependOn'] = $field->getDisabledOn()->getName();
        }

        if (!$field->getDisabledFields()->isEmpty()) {
            $options['attr']['data-onChange'] = $field->getName();
            $options['attr']['data-value'] = $field->getEmptyData();
        }

        if (!$endblock && !is_null($nextField) && $field->getOrderNumber() == $nextField->getOrderNumber()) {
            $options['attr']['beginblock'] = true;
            $endblock = true;
        }

        if ($endblock && ((!is_null($nextField) && $field->getOrderNumber() != $nextField->getOrderNumber()) || is_null($nextField))) {
            $options['attr']['endblock'] = true;
            $endblock = false;
        }

        if ($this->isImageChoiceType($field)) {
            $options['renovation_types'] = $field->getRenovations()->toArray();
        }

        if ($this->isTextChoiceType($field)) {
            $options['choices'] = [];
            $options['choice_translation_domain'] = false;
            foreach ($field->getChoices() as $fieldChoice) {
                $options['choices'][$fieldChoice->getChoiceLabel()] =  $fieldChoice->getChoiceValue();
            }
        }

        return $options;
    }

    /**
     * Set a dynamic field options grouped by the order number
     *
     * @param Field       $field        The current field
     *
     * @return array
     */
    public function getConstraintsOptions(Field $field): array
    {
        $options = [
            'label' => $field->getLabelText(),
            'mapped' => $field->getMapped(),
            'translation_domain' => false
        ];

        $constraint = $this->getFieldConstraintType($field);

        if (!is_null($constraint)){
            $options["constraints"] = [
                new $constraint['class'] ($constraint['options'])
            ];
        }


        if ($this->isImageChoiceType($field)) {
            $options['renovation_types'] = $field->getRenovations()->toArray();
        }

        if ($this->isTextChoiceType($field)) {
            $options['choices'] = [];
            $options['choice_translation_domain'] = false;
            foreach ($field->getChoices() as $fieldChoice) {
                $options['choices'][$fieldChoice->getChoiceLabel()] =  $fieldChoice->getChoiceValue();
            }
        }

        return $options;
    }

    /**
     * Get the default form values
     *
     * @param Retouch $retouch
     * @return array
     */
    public function getDefaultData(Retouch $retouch): array
    {
        $options = array();
        foreach ($this->fieldRepository->findDefaultValuesFromRetouch($retouch->getId()) as $field) {
            $options[$field->getName()] = $field->getEmptyData();
        }
        return $options;
    }

    /**
     * Check the field type if has images choices
     *
     * @param Field $field
     *
     * @return bool
     */
    private function isImageChoiceType(Field $field): bool
    {
        return in_array($field->getFieldType(), $this->dynamicFormTypeOptions['renovation_type']);
    }

    /**
     * Check the field type if has choices
     *
     * @param Field $field
     *
     * @return bool
     */
    private function isTextChoiceType(Field $field): bool
    {
        return in_array($field->getFieldType(), $this->dynamicFormTypeOptions['choice_type']);
    }

    /**
     * Get the field constraint
     *
     * @param Field $field
     * @return array|null
     */
    private function getFieldConstraintType(Field $field): ?array
    {
        if (array_key_exists($field->getFieldType(), $this->dynamicFormConstraints)) {
            return array(
                'class' => $this->dynamicFormConstraints[$field->getFieldType()]['class'],
                'options' => $this->dynamicFormConstraints[$field->getFieldType()]['options'] ?? []
            );
        }
        return null;
    }

    /**
     * Get the form fields grouped by field group
     *
     * @param string    $local      The current local en|fr
     * @param Retouch   $retouch
     *
     * @return null|array
     */
    public function getDefaultFields(string $local, Retouch $retouch): ?array
    {
        return $this->fieldGroupRepository->findFormFieldsByRetouch($local, $retouch);
    }
}
