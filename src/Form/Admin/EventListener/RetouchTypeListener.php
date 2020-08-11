<?php
namespace App\Form\Admin\EventListener;

use App\Entity\Retouch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RetouchTypeListener implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
        $retouch = $event->getData();
        $form = $event->getForm();

        $title = null;
        $description = null;

        if ($retouch->getId() !== null) {
            $repository = $this->entityManager->getRepository('Gedmo\Translatable\Entity\Translation');
            $translations = $repository->findTranslations($retouch);

            if (isset($translations['en'])) {
                $title = $translations['en']['title'] ?? $title;
                $description = $translations['en']['description'] ?? $description;
            }
        }

        $form
            ->add('title_en', TextType::class, [
                'label' => 'admin.retouch.title_en',
                'mapped' => false,
                'data' => $title,
                'constraints' => [new Assert\NotBlank()]
            ])
            ->add('description_en', TextareaType::class, [
                'label' => 'admin.retouch.description_en',
                'attr' => ['class' => 'js-wyswyg-selector'],
                'mapped' => false,
                'data' => $description
            ])
        ;

        if (!is_null($retouch->getAppType()) && $retouch->getAppType() === Retouch::IMMOSQUARE_TYPE) {
            $this->addRetouchCodeField($form);
        }

        if (!is_null($retouch->getAppType()) && $retouch->getAppType() === Retouch::EMMOBILIER_TYPE) {
            $this->addOrderNumber($form);
        }
    }

    public function onPreSubmit(FormEvent $event)
    {
        $retouch = $event->getData();
        $form = $event->getForm();

        if (!$retouch) {
            return;
        }

        if (isset($retouch['appType']) && $retouch['appType'] === Retouch::IMMOSQUARE_TYPE) {
            $this->addRetouchCodeField($form);
        }

        if (isset($retouch['appType']) && $retouch['appType'] === Retouch::EMMOBILIER_TYPE) {
            $this->addOrderNumber($form);
        }
    }

    private function addRetouchCodeField(FormInterface &$form){

        if ($form->has('orderNumber')){
            $form->remove('orderNumber');
        }

        $form
            ->add('retouchCode', TextType::class, [
                'label' => 'admin.retouch.retouch_code',
                'constraints' => [new Assert\NotBlank(['groups' => ['retouchCreation']])]
            ])
        ;
    }

    private function addOrderNumber(FormInterface &$form){

        if ($form->has('retouchCode')){
            $form->remove('retouchCode');
        }

        $form
            ->add('orderNumber', IntegerType::class, [
                'label' => 'admin.retouch.order_number',
                'constraints' => [new Assert\NotBlank(['groups' => ['retouchCreation']])]
            ])
        ;
    }
}
