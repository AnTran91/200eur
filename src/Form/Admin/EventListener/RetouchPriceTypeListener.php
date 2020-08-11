<?php

namespace App\Form\Admin\EventListener;

use App\Entity\OrderDeliveryTime;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

class RetouchPriceTypeListener implements EventSubscriberInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Constructor
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT   => 'onPreSubmit'
        );
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $retouchPrice = $event->getData();

        $type = OrderDeliveryTime::DEFAULT_APP_TYPE;
        if (!is_null($retouchPrice) && !empty($retouchPrice->getRetouch()->getAppType())) {
            $type = $retouchPrice->getRetouch()->getAppType();
        }

        $this->addOrderDeliveryTimeField($form, $type);
    }

    public function onPreSubmit(FormEvent $event)
    {
        $parentForm = $event->getForm()->getParent()->getParent();
        $form = $event->getForm();

        $type = OrderDeliveryTime::DEFAULT_APP_TYPE;
        if (!is_null($parentForm->get('appType')->getData()) && !empty($parentForm->get('appType')->getData())) {
            $type = $parentForm->get('appType')->getData();
        }

        $this->addOrderDeliveryTimeField($form, $type);
    }

    /**
     * @param FormInterface     $form
     * @param string            $type
     */
    private function addOrderDeliveryTimeField(FormInterface &$form, string $type){
        if ($form->has('orderDeliveryTime')){
            $form->remove('orderDeliveryTime');
        }

        $form->add('orderDeliveryTime', EntityType::class, array(
            'label' => 'admin.retouch.order_delivery_time',
            'placeholder' => 'admin.common.none',
            'class' => OrderDeliveryTime::class,
            'query_builder' => function (EntityRepository $er) use ($type) {
                return $er->createQueryBuilder('u')
                    ->where('u.appType = :appType')
                    ->setParameter(":appType", $type);
            },
            'choice_label' => function (OrderDeliveryTime $deliveryTime) {
                return $this->translator->trans('admin.retouch.delivery', [
                    '%time' => $deliveryTime->getTime() ,
                    '%unit' => $this->translator->trans($deliveryTime->getUnit(), [], 'admin')
                ], 'admin') ;
            }
        ));
    }
}