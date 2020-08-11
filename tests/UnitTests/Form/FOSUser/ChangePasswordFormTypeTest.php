<?php

namespace Tests\UnitTests\Form\FOSUser;

use FOS\UserBundle\Form\Type\ChangePasswordFormType;
use Tests\UnitTests\Form\FOSUser\Entity\TestUser;

class ChangePasswordFormTypeTest extends ValidatorExtensionTypeTestCase
{
    public function testSubmit()
    {
        $user = new TestUser();
        $user->setPassword('foo');
        $form = $this->factory->create(ChangePasswordFormType::class, $user);
        $formData = array(
            'current_password' => 'foo',
            'plainPassword' => array(
                'first' => 'bar',
                'second' => 'bar',
            ),
        );
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertSame($user, $form->getData());
        $this->assertSame('bar', $user->getPlainPassword());
    }
    /**
     * @return array
     */
    protected function getTypes()
    {
        return array_merge(parent::getTypes(), array(
            new ChangePasswordFormType('Tests\UnitTests\Form\FOSUser\Entity\TestUser'),
        ));
    }
}
