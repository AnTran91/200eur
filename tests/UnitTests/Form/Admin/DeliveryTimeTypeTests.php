<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Form;

use App\Form\Admin\DeliveryTimeType;
use App\Entity\OrderDeliveryTime;

use Symfony\Component\Form\Test\TypeTestCase;

use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Tests\UnitTests\Form\Traits\ValidatorExtensionTrait;
use Tests\UnitTests\Form\Traits\EntityTypeExtensionTrait;

class DeliveryTimeTypeTests extends TypeTestCase
{
    use ValidatorExtensionTrait;

    protected $choices = [
      'time.hour' => 'time.hour',
      'time.day' => 'time.day',
      'time.week' => 'time.week'
    ];

    protected function getExtensions()
    {
        // Mock the FormType: entity
        $deliveryTime = new DeliveryTimeType($this->choices);

        return array(
            new ValidatorExtension($this->mockValidator()),
            // register the type instances with the PreloadedExtension
            new PreloadedExtension(array($deliveryTime), array()),
        );
    }

    /**
     * Test transction form submitions with valid data expecte success
     *
     * @dataProvider getValidDataProvider
     */
    public function testSubmitValidData($formData)
    {
        $deliveryTimeToCompare = new OrderDeliveryTime();
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(DeliveryTimeType::class, $deliveryTimeToCompare);

        $deliveryTime = new OrderDeliveryTime();

        $deliveryTime->setTime($formData['time']);
        $this->assertSame($formData['time'], $deliveryTime->getTime());

        $deliveryTime->setUnit($formData['unit']);
        $this->assertSame($formData['unit'], $deliveryTime->getUnit());

        $deliveryTime->setGlobal($formData['global']);
        $this->assertSame($formData['global'], $deliveryTime->getGlobal());

        $deliveryTime->setSelectedByDefault($formData['selectedByDefault']);
        $this->assertSame($formData['selectedByDefault'], $deliveryTime->getSelectedByDefault());

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($deliveryTime, $deliveryTimeToCompare);

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
          'time' => 20,
          'unit' => 'time.hour',
          'global' => true,
          'selectedByDefault' => true
        )];
        yield [array(
          'time' => null,
          'unit' => null,
          'global' => null,
          'selectedByDefault' => null
        )];
    }
}
