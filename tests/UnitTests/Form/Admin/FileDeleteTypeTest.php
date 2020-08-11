<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Form;

use App\Form\Admin\FileDeleteType;

use Symfony\Component\Form\Test\TypeTestCase;

class FileDeleteTypeTest extends TypeTestCase
{
    /**
     * Test transction form submitions with valid data expecte success
     *
     * @dataProvider getValidDataProvider
     */
    public function testSubmitValidData($formData)
    {
        $form = $this->factory->create(FileDeleteType::class);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
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
          'uuid' => '7b1aa81d-73ca-4372-974a-b71ccadaf7a4',
          'file_name' => 'test.jpg'
        )];
        yield [array(
          'uuid' => 'be22a32f-8ac7-460f-963b-2e377decefa1',
          'file_name' => 'test.jpg'
        )];
    }
}
