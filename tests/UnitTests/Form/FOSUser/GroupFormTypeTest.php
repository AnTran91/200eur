<?php

namespace Tests\UnitTests\Form\FOSUser;

use FOS\UserBundle\Form\Type\GroupFormType;
use Tests\UnitTests\Form\FOSUser\Entity\TestGroup;

class GroupFormTypeTest extends TypeTestCase
{
    public function testSubmit()
    {
        $group = new TestGroup('foo');
        $form = $this->factory->create(GroupFormType::class, $group);
        $formData = array(
            'name' => 'bar',
        );
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertSame($group, $form->getData());
        $this->assertSame('bar', $group->getName());
    }
    /**
     * @return array
     */
    protected function getTypes()
    {
        return array_merge(parent::getTypes(), array(
            new GroupFormType('Tests\UnitTests\Form\FOSUser\Entity\TestGroup'),
        ));
    }
}
