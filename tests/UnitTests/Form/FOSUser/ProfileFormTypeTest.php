<?php

namespace Tests\UnitTests\Form\FOSUser;

use FOS\UserBundle\Form\Type\ProfileFormType;
use Tests\UnitTests\Form\FOSUser\Entity\TestUser;

class ProfileFormTypeTest extends ValidatorExtensionTypeTestCase
{
    public function testSubmit()
    {
        $user = new TestUser();
        $form = $this->factory->create(ProfileFormType::class, $user);
        $formData = array(
            'username' => 'bar',
            'email' => 'john@doe.com',
        );
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertSame($user, $form->getData());
        $this->assertSame('bar', $user->getUsername());
        $this->assertSame('john@doe.com', $user->getEmail());
    }
    /**
     * @return array
     */
    protected function getTypes()
    {
        return array_merge(parent::getTypes(), array(
            new ProfileFormType('Tests\UnitTests\Form\FOSUser\Entity\TestUser'),
        ));
    }
}
