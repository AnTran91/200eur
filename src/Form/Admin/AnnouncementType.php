<?php

namespace App\Form\Admin;

use App\Entity\Announcement;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;


class AnnouncementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
              'label' => 'admin.announcement.title',
              'required' => false,
              'empty_data' => '',
            ])
            ->add('body', CKEditorType::class, [
              'label' => 'admin.announcement.content',
              'required' => false,
              'empty_data' => '',
            ])
            ->add('startDate', DateType::class, [
              'label' => 'admin.common.start_date',
              'widget' => 'single_text',
              // prevents rendering it as type="date", to avoid HTML5 date pickers
              'html5' => false,
              'format' => 'dd/MM/yyyy',
              // adds a class that can be selected in JavaScript
              'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off'],
            ])
            ->add('endDate', DateType::class, [
              'label' => 'admin.common.end_date',
              'widget' => 'single_text',
              // prevents rendering it as type="date", to avoid HTML5 date pickers
              'html5' => false,
              'format' => 'dd/MM/yyyy',
              // adds a class that can be selected in JavaScript
              'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off'],
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'Active',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Announcement::class,
            'translation_domain' => 'admin',
        ]);
    }
}
