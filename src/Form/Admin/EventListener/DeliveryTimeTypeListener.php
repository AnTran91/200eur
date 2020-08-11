<?php
namespace App\Form\Admin\EventListener;

use App\Entity\OrderDeliveryTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints as Assert;

class DeliveryTimeTypeListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT   => 'onPreSubmit'
        );
    }

    public function onPreSetData(FormEvent $event)
    {
        $deliveryTime = $event->getData();
        $form = $event->getForm();

        if (!is_null($deliveryTime->getAppType()) && $deliveryTime->getAppType() === OrderDeliveryTime::IMMOSQUARE_TYPE) {
            $this->addOrderDeliveryCodeField($form);
        }

        if (!is_null($deliveryTime->getAppType()) && $deliveryTime->getAppType() === OrderDeliveryTime::EMMOBILIER_TYPE) {
            $this->removeOrderDeliveryCodeField($form);
        }
    }

    public function onPreSubmit(FormEvent $event)
    {
        $deliveryTime = $event->getData();
        $form = $event->getForm();

        if (!$deliveryTime) {
            return;
        }

        if (isset($deliveryTime['appType']) && $deliveryTime['appType'] === OrderDeliveryTime::IMMOSQUARE_TYPE) {
            $this->addOrderDeliveryCodeField($form);
        }

        if (isset($deliveryTime['appType']) && $deliveryTime['appType'] === OrderDeliveryTime::EMMOBILIER_TYPE) {
            $this->removeOrderDeliveryCodeField($form);
        }
    }

    private function addOrderDeliveryCodeField(FormInterface &$form){
        $form->add('orderDeliveryCode', TextType::class, [
            'label' => 'admin.delivery_time.order_delivery_code',
            'constraints' => [new Assert\NotBlank()]
        ])
        ;
    }

    private function removeOrderDeliveryCodeField(FormInterface &$form){

        if ($form->has('orderDeliveryCode')){
            $form->remove('orderDeliveryCode');
        }
    }
}
