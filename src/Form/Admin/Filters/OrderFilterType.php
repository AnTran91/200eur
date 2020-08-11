<?php

namespace App\Form\Admin\Filters;

use App\Entity\User;
use App\Entity\OrderDeliveryTime;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\ORM\EntityRepository;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;

use App\Form\Shared\Type\OrderStatusType;
use Symfony\Component\Translation\TranslatorInterface;

use Symfony\Component\HttpFoundation\Session\Session;

class OrderFilterType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $userInChargeOnTheOrderSection;

    /**
     * @var array
     */
    private $appTypes;

    /**
     * Constructor
     *
     * @param TranslatorInterface   $translator
     * @param array                 $userInChargeOnTheOrderSection
     * @param array                 $applicationsTypes
     */
    public function __construct(TranslatorInterface $translator, array $userInChargeOnTheOrderSection, array $applicationsTypes)
    {
        $this->translator = $translator;
        $this->userInChargeOnTheOrderSection = $userInChargeOnTheOrderSection;
        $this->appTypes = $applicationsTypes;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $userInChargeOnTheOrderSection = $this->userInChargeOnTheOrderSection;
        $session = new Session();
        $builder
            ->add('isRecharge', ChoiceType::class, [
              'label' => 'admin.order.order_type',
              'choices' => ['Prestations' => 0, 'Tirelire' => 1],
              'placeholder' => $session->get('isRecharge') === 1 ? 'Tirelire' : ($session->get('isRecharge') === 0 ? 'Prestations' : 'admin.common.none'),
              'attr' => ['class' => 'js-select-list']
            ])
            ->add('orderNumber', TextType::class, [
              'label' => 'admin.order.id'
            ])
            ->add('orderStatus', OrderStatusType::class, [
              'label' => 'admin.order.status',
              'translation_domain' => 'admin',
              'placeholder' => 'admin.common.show_all'
            ])
            ->add('client', EntityType::class, [
              'class' => User::class,
              'label' => 'admin.order.client',
              'placeholder' => 'admin.common.show_all',
              'attr' => ['class' => 'js-select-list'],
              'query_builder' => function (EntityRepository $er) {
                  return $er->createQueryBuilder('u')
                          ->addSelect('wallet')
                          ->join('u.wallet', 'wallet')
                          ->orderBy('u.email', 'ASC');
              }
            ])
            ->add('affectedTo', EntityType::class, [
              'class' => User::class,
              'label' => 'admin.order.in_charge',
	            'attr' => ['class' => 'js-select-list'],
              'placeholder' => 'admin.common.show_all',
              'query_builder' => function (EntityRepository $er) use($userInChargeOnTheOrderSection) {
                  $qb = $er->createQueryBuilder('u');
                  foreach ($userInChargeOnTheOrderSection as $role) {
                      $qb->orWhere($qb->expr()->like('u.roles', ":role{$role}"))
                      ->setParameter(":role{$role}", "%{$role}%");
                  }
                  return $qb->addSelect('wallet')
                          ->join('u.wallet', 'wallet')
                          ->orderBy('u.email', 'ASC');
              },
	          'choice_label' => function (User $entity, $key, $value) {
		          return (string) $entity->getFormattedFullName();
		      }
            ])
            ->add('creationDate', DateIntervalType::class, [
              'label' => 'admin.common.date',
              'widget' => 'single_text',
              // adds a class that can be selected in JavaScript
              'attr' => ['class' => 'js-datepicker form-control', 'autocomplete' => 'off'],
            ])
            ->add('deliveryTime', EntityType::class, array(
              'label' => 'admin.order.time',
              'class' => OrderDeliveryTime::class,
              'choice_label' => function (OrderDeliveryTime $deliveryTime) {
                  return $this->translator->trans('orders.field.delivery', ['%time' => sprintf('%s%s', $deliveryTime->getTime(), $this->translator->trans($deliveryTime->getUnit()))]) ;
              },
              'query_builder' => function (EntityRepository $er) {
                  return $er->createQueryBuilder('u')
                      ->where('u.global = True')
                      ->orderBy('u.time', 'ASC');
              },
              'choice_attr' => function (OrderDeliveryTime $entity, $key, $value) {
	            // adds a class like attending_yes, attending_no, etc
	            if (!is_null($entity->getAppType())) {
		            return ['class' => $entity->getAppType()];
	            }
	            return [];
              },
              'placeholder' => 'admin.common.show_all',
	            'attr' => ['class' => 'js-order-delivery-time'],
              'required' => false
            ))
            ->add('appType', ChoiceType::class, [
                'label' => 'admin.application.type',
                'choices' => $this->appTypes,
	            'attr' => ['class' => 'js-order-delivery-time-type']
            ])
            ->add('itemsPerPage', ChoiceType::class, [
                'label' => 'admin.paginator.title',
                'choices' => [20 => 20, 50 => 50, 'admin.paginator.all' => 'all'],
                'attr' => ['class' => 'js-number-of-rows']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'admin',
            'csrf_protection' => false
        ]);
    }

    /**
     * This will remove formTypeName from the form
     *
     * @return null|string
     */
    public function getBlockPrefix(): ?string
    {
        return 'filter';
    }
}
