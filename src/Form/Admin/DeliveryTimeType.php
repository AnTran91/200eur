<?php
namespace App\Form\Admin;

use App\Entity\OrderDeliveryTime;

use App\Form\Admin\EventListener\DeliveryTimeTypeListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DeliveryTimeType extends AbstractType
{
    /**
     * @var array
     */
    private $applicationsTypeOptions;

    /**
     * @var array
     */
    private $choices;

    /**
     * DeliveryTimeType constructor.
     *
     * @param array $orderDeliveryTimeUnit
     * @param array $applicationsTypes
     */
    public function __construct(array $orderDeliveryTimeUnit, array $applicationsTypes)
    {
        $this->applicationsTypeOptions = $applicationsTypes;
        $this->choices = $orderDeliveryTimeUnit;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('time', TextType::class, ['label' => 'admin.delivery_time.time'])
          ->add('unit', ChoiceType::class, [
            'label' => 'admin.delivery_time.unit',
            'choices'  => $this->choices,
          ])
          ->add('appType', ChoiceType::class, [
            'choices' => $this->applicationsTypeOptions,
            'label' => 'admin.retouch.retouch_type',
            'attr' => ['class' => 'js-app-type']
          ])
          ->add('global', CheckboxType::class, ['label' => 'admin.delivery_time.globals'])
          ->add('selectedByDefault', CheckboxType::class, ['label' => 'admin.delivery_time.default'])
          ->addEventSubscriber(new DeliveryTimeTypeListener())
          ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
          'class' => OrderDeliveryTime::class,
          'translation_domain' => 'admin',
      ]);
    }
}
