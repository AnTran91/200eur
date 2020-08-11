<?php

namespace App\Form\Admin;

use App\Entity\Holidays;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class HolidaysType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'admin.holidays.title'])
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Holidays::class,
            'translation_domain' => 'admin',
        ]);
    }
}
