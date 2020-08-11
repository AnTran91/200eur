<?php

namespace App\Form\Admin;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Production;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Form\Shared\Type\OrderStatusType;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Doctrine\ORM\EntityRepository;

class OrderType extends AbstractType
{
    /**
     * @var string
     */
    private $userInChargeOnTheOrderSection;

    /**
     * Constructor
     *
     * @param array $userInChargeOnTheOrderSection
     */
    public function __construct(array $userInChargeOnTheOrderSection)
    {
        $this->userInChargeOnTheOrderSection = $userInChargeOnTheOrderSection;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $userInChargeOnTheOrderSection = $this->userInChargeOnTheOrderSection;

        $builder
            ->add('orderStatus', OrderStatusType::class, [
              'label' => 'admin.order.status',
              'translation_domain' => 'admin'
            ])
            ->add('affectedTo', EntityType::class, [
              'class' => User::class,
              'label' => 'admin.order.in_charge',
              'placeholder' => 'admin.order.not_affected',
              'query_builder' => function (EntityRepository $er) use ($userInChargeOnTheOrderSection) {
                  $qb = $er->createQueryBuilder('u');
                  foreach ($userInChargeOnTheOrderSection as $key => $role) {
                      $qb->orWhere($qb->expr()->like('u.roles', ":role_{$key}"))
                      ->setParameter(":role_{$key}", "%{$role}%");
                  }
                  return $qb->addSelect('wallet')
                          ->join('u.wallet', 'wallet')
                          ->orderBy('u.email', 'ASC');
              },
	          'choice_label' => function (User $entity, $key, $value) {
		          return (string) $entity->getFormattedFullName();
              }
            ])
            ->add('production', EntityType::class, [
              'class' => Production::class,
              'label' => 'admin.production.production',
              'placeholder' => 'admin.production.empty_field'
            ])
            ->add('pictures', CollectionType::class, array(
                'label' => false,
                'entry_type' => PictureType::class,
                'entry_options' => array(
                  'label' => false
                ),
                'allow_add' => false,
                'allow_delete' => false,
                'by_reference' => false,
                'attr'         => [
                   'class' => "js-pictures-collection row",
               ],
            ))
            ->add('updatePrice', SubmitType::class, [
                "label" => "admin.order.actions.update_and_recalculate_order_price",
                "attr" => ["class" => "btn btn-secondary"]
            ])
            ->add('update', SubmitType::class, [
                "label" => "admin.order.actions.update_order",
                "attr" => ["class" => "btn btn-primary"]
            ])
            ->add('cancel', ResetType::class, [
                "label" => "admin.form.reset",
                "attr" => ["class" => "btn btn-danger"]
            ])
        ;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'translation_domain' => 'admin'
        ]);
    }
}
