<?php
namespace App\Form\Admin\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Validator\Constraints as Assert;

use App\Entity\Promo;
use App\Entity\Network;
use App\Entity\Agency;
use App\Entity\User;

class PictureDiscountTypeListener implements EventSubscriberInterface
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
        $promo = $event->getData();
        $form = $event->getForm();

        if (!$promo->getId()) {
            $form->remove('expired');
        }

        if (!is_null($promo->getPromoType()) && $promo->getPromoType() === Promo::ASSIGN_TO_NETWORK) {
            $form->add('organization', EntityType::class, [
              'label' => 'admin.promo.network',
              'class' => Network::class,
              'attr' => ['class' => 'js-select'],
              'placeholder' => 'admin.promo.network_select',
              'constraints' => [new Assert\NotBlank()]
            ]);
        } elseif (!is_null($promo->getPromoType()) && $promo->getPromoType() === Promo::ASSIGN_TO_AGENCY) {
            $form->add('organization', EntityType::class, [
              'label' => 'admin.promo.agency',
              'class' => Agency::class,
              'attr' => ['class' => 'js-select'],
              'placeholder' => 'admin.promo.agency_select',
              'constraints' => [new Assert\NotBlank()]
            ]);
        } elseif (!is_null($promo->getPromoType()) && $promo->getPromoType() === Promo::ASSIGN_TO_SPECIFIC_CUSTOMERS) {
            $form->add('clients', EntityType::class, [
              'label' => 'admin.promo.clients',
              'class' => User::class,
              'multiple' => true,
              'attr' => ['placeholder' => 'admin.promo.clients_select','class' => 'js-select']
            ]);
        }

        if (!is_null($promo->getHasNumberOfUse()) && false == $promo->getHasNumberOfUse()) {
            $form->remove('useLimitPerUser');
            $form->remove('useLimit');
        }
    }

    public function onPreSubmit(FormEvent $event)
    {
        $promo = $event->getData();
        $form = $event->getForm();

        if (!$promo) {
            return;
        }

        if (isset($promo['promoType']) && $promo['promoType'] === Promo::ASSIGN_TO_NETWORK) {
            $form->add('organization', EntityType::class, [
              'label' => 'admin.promo.network',
              'class' => Network::class,
              'attr' => ['class' => 'js-select'],
              'placeholder' => 'admin.promo.network_select',
              'constraints' => [new Assert\NotBlank()]
            ]);
        } elseif (isset($promo['promoType']) && $promo['promoType'] === Promo::ASSIGN_TO_AGENCY) {
            $form->add('organization', EntityType::class, [
              'label' => 'admin.promo.agency',
              'class' => Agency::class,
              'attr' => ['class' => 'js-select'],
              'placeholder' => 'admin.promo.agency_select',
              'constraints' => [new Assert\NotBlank()]
            ]);
        } elseif (isset($promo['promoType']) && $promo['promoType'] === Promo::ASSIGN_TO_SPECIFIC_CUSTOMERS) {
            $form->add('clients', EntityType::class, [
              'label' => 'admin.promo.clients',
              'class' => User::class,
              'multiple' => true,
              'attr' => ['placeholder' => 'admin.promo.clients_select','class' => 'js-select']
            ]);
        }

        if (!isset($promo['hasNumberOfUse']) || false == $promo['hasNumberOfUse']) {
            $form->remove('useLimitPerUser');
            $form->remove('useLimit');
        }
    }
}
