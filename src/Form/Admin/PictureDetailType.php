<?php

namespace App\Form\Admin;

use App\Entity\PictureDetails;
use App\Entity\Retouch;
use App\Entity\ParamCollection;

use App\Form\Shared\Type\DonePictureType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use App\Handlers\DynamicFormHandler;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Doctrine\ORM\EntityRepository;

class PictureDetailType extends AbstractType
{
    /**
     * @var DynamicFormHandler
     */
    public $formFactory;

    public function __construct(DynamicFormHandler $formFactory)
    {
      $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price', MoneyType::class, array(
                'label' => 'admin.order.amount',
                'attr' => ['class' => 'js-retouch-price'],
            ))

            ->add('returnedPicture', DonePictureType::class, array(
                'label' => 'admin.picture.name'
            ))

            ->add('returnedGifPicture', DonePictureType::class, array(
                'label' => 'admin.picture.gif_name'
            ))

            ->add('returnedMP4Picture', DonePictureType::class, array(
                'label' => 'admin.picture.mp4_name'
            ))

            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                array($this, 'onPreSetData')
            )
            ->addEventListener(
                FormEvents::SUBMIT,
                array($this, 'onPreSubmit')
            )
        ;
    }

    public function onPreSetData(FormEvent $event)
    {
        $pictureDetail = $event->getData();
        $form = $event->getForm();

        $type = Retouch::DEFAULT_APP_TYPE;
        if (!is_null($pictureDetail) && !is_null($pictureDetail->getPicture()) && !is_null($pictureDetail->getPicture()->getOrder())){
            $type = $pictureDetail->getPicture()->getOrder()->getAppType();
        }

        $form->add('retouch', EntityType::class, array(
            'class' => Retouch::class,
            'label' => 'admin.retouch.retouches_list',
            'attr' => ['class' => 'js-retouch-options'],
            'query_builder' => function (EntityRepository $er) use ($type) {
                return $er->createQueryBuilder('r')
                    ->addSelect('p')
                    ->join('r.pricings', 'p')
                    ->where('r.appType = :appType')
                    ->setParameter(":appType", $type);
            },
            'choice_attr' => function (Retouch $entity, $key, $value) {
                if ($entity->getPricings()->count() === 1) {
                    return ['data-standard' => $entity->getPricings()->first()->getPrice()];
                }
	
	            $result = [];
                foreach ($entity->getPricings() as $pricing) {
                    $result[sprintf('data-%s', $pricing->getOrderDeliveryTime()->getId())] = $pricing->getPrice();
                }
                return $result;
            },
        ));
    }

    public function onPreSubmit(FormEvent $event)
    {
        $pictureDetail = $event->getData();

        if (!is_null($pictureDetail->getParam())) {
            return;
        }

        $pictureDetail->setParam(
          (new ParamCollection())
            ->setElements($this->formFactory->getDefaultData($pictureDetail->getRetouch()))
        );

        $event->setData($pictureDetail);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PictureDetails::class,
            'translation_domain' => 'admin',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'picture_detail';
    }
}
