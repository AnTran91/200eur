<?php

namespace App\Form\Shared\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class RoleType extends AbstractType
{
    /**
     * @var array
     */
    private $_roleHierarchy;

    /**
     * @param array $roleHierarchy
     */
    public function setConfiguration(array $roleHierarchy)
    {
        $this->_roleHierarchy = $roleHierarchy;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices'       => $this->getExistingRoles(),
        ));
    }

    /**
     * @return array
     */
    private function getExistingRoles()
    {
        $theRoles = array();
        $roles = array_keys($this->_roleHierarchy);
        foreach ($roles as $role) {
            $theRoles[$role] = $role;
        }
        return $theRoles;
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
