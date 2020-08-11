<?php

namespace App\Form\Shared\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Doctrine\ORM\EntityManagerInterface;

use App\Repository\RetouchRepository;

class RetouchEntityType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $retouchRepository;

    // /**
    //  * @var RetouchTransformer
    //  */
    // private $transformer;

    public function __construct(RetouchRepository $retouchRepository/*, RetouchTransformer $transformer*/)
    {
        $this->retouchRepository = $retouchRepository;
        //$this->transformer = $transformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$builder->addModelTransformer($this->transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
          // used to render a select box, check boxes or radios
          'choices' => array_combine(array_column($this->retouchRepository->findByEmmobilierType(), 'id'), array_column($this->retouchRepository->findByEmmobilierType(), 'id')),
          'multiple' => true,
          'row' => 4,
          'current_locale' => 'fr'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['retouchs'] = $this->buildViewFormatter($options['current_locale']);
        $view->vars['row'] = $options['row'];
    }

    /**
     * {@inheritdoc}
     */
    public function buildViewFormatter(string $locale): array
    {
        $formattedResult = array();
        foreach ($this->retouchRepository->findByEmmobilierTypeWithFallback($locale) as $retouch) {
            $formattedResult[] = [
                'value' => $retouch->getId(),
                'label' => $retouch->getTitle(),
                'info' => $retouch->getDescription(),
                'pricing' => $retouch->getPricings(),
                'link' => $retouch->getHelpLink(),
            ];
        }
        return $formattedResult;
    }


    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'retouchs_block';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
