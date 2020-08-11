<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Form;

use App\Form\Admin\MonthlyOrderType;
use App\Entity\Order;

use Symfony\Component\Form\Test\TypeTestCase;

use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Tests\UnitTests\Form\Traits\ValidatorExtensionTrait;
use Tests\UnitTests\Form\Traits\EntityTypeExtensionTrait;



class MonthlyOrderTypeTest extends TypeTestCase
{
    use ValidatorExtensionTrait, EntityTypeExtensionTrait;

    protected function getExtensions()
    {
        // Mock the FormType: entity
        $entityType = new EntityType($this->mockRegistry($this->getUsers()));

        return array(
            new ValidatorExtension($this->mockValidator()),
            // register the type instances with the PreloadedExtension
            new PreloadedExtension(array('entity' => $entityType), array()),
        );
    }

    /**
     * Test transction form submitions with valid data expecte success
     *
     * @dataProvider getValidDataProvider
     */
    public function testSubmitValidData($formData)
    {
      $form = $this->factory->create(MonthlyOrderType::class);

      // submit the data to the form directly
      $form->submit($formData);
      $this->assertTrue($form->isSynchronized());

      $this->assertEquals($form->getData(), $formData);

      $view = $form->createView();
      $children = $view->children;

      foreach (array_keys($formData) as $key) {
          $this->assertArrayHasKey($key, $children);
      }
    }

    /**
     * Return Different Sets of Data
     *
     * @return array
     */
    public function getValidDataProvider()
    {
        yield [array(
          'title' => 'test',
          'startDate' => new \DateTime('now'),
          'endDate' => new \DateTime('2018-12-12'),
        ),];
        yield [array(
          'title' => 'test2',
          'startDate' => new \DateTime('now'),
          'endDate' => new \DateTime('2018-12-13'),
        ),];
    }

    /**
     * @return User[] mocked user
     */
    public function getUsers()
    {
        $userCollection  = new ArrayCollection();

        $userCollection->add($this->getUser(1));
        $userCollection->add($this->getUser(2));
        $userCollection->add($this->getUser(3));

        return $userCollection;
    }

    /**
     * @return User mocked
     */
    public function getUser(int $id)
    {
        $mockUserEntity = $this->createMock('App\Entity\User');
        $mockUserEntity->expects($this->any())->method('getId')
          ->will($this->returnValue($id));

        return $mockUserEntity;
    }

}
