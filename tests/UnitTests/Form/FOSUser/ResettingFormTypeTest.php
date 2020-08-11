<?php

namespace Tests\UnitTests\Form\FOSUser;

use FOS\UserBundle\Form\Type\ResettingFormType;
use Tests\UnitTests\Form\FOSUser\Entity\TestUser;

class ResettingFormTypeTest extends ValidatorExtensionTypeTestCase
{
    public function testSubmit()
    {
        $user = new TestUser();
        $form = $this->factory->create(ResettingFormType::class, $user);
        $formData = array(
            'plainPassword' => array(
                'first' => 'test',
                'second' => 'test',
            ),
        );
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertSame($user, $form->getData());
        $this->assertSame('test', $user->getPlainPassword());
    }
    /**
     * @return array
     */
    protected function getTypes()
    {
        return array_merge(parent::getTypes(), array(
            new ResettingFormType('Tests\UnitTests\Form\FOSUser\Entity\TestUser'),
        ));
    }
}
