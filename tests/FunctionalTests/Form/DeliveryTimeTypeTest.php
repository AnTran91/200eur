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
use Symfony\Component\Form\Form;


class DeliveryTimeTypeTest extends TypeTestCase
{
    /**
     * Test retouch form submitions with valid data expecte success
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
        $deliveryTime->setUnit($formData['unit']);
        $deliveryTime->setGlobal($formData['global']);
        $deliveryTime->setSelectedByDefault($formData['selectedByDefault']);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());

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
        yield [array('time' => '24', 'unit' => 'h', 'global' => true, 'selectedByDefault' => true)];
        yield [array('time' => '48', 'unit' => 'h', 'global' => true, 'selectedByDefault' => false)];
    }
}
