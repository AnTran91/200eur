<?php

namespace Tests\UnitTests\Form\FOSUser;

use FOS\UserBundle\Form\Type\RegistrationFormType;
use Tests\UnitTests\Form\FOSUser\Entity\TestUser;

class RegistrationFormTypeTest extends ValidatorExtensionTypeTestCase
{
    public function testSubmit()
    {
        $user = new TestUser();
        $form = $this->factory->create(RegistrationFormType::class, $user);
        $formData = array(
            'username' => 'bar',
            'email' => 'john@doe.com',
            'plainPassword' => array(
                'first' => 'test',
                'second' => 'test',
            ),
        );
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertSame($user, $form->getData());
        $this->assertSame('bar', $user->getUsername());
        $this->assertSame('john@doe.com', $user->getEmail());
        $this->assertSame('test', $user->getPlainPassword());
    }
    /**
     * @return array
     */
    protected function getTypes()
    {
        return array_merge(parent::getTypes(), array(
            new RegistrationFormType('Tests\UnitTests\Form\FOSUser\Entity\TestUser'),
        ));
    }
}
