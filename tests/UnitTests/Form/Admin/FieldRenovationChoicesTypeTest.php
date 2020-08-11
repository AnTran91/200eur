<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Form;

use App\Form\Admin\FieldRenovationChoicesType;
use App\Entity\FieldRenovationChoices;

use Symfony\Component\Form\Test\TypeTestCase;

use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Tests\UnitTests\Form\Traits\ValidatorExtensionTrait;
use Tests\UnitTests\Form\Traits\EntityTypeExtensionTrait;



class FieldRenovationChoicesTypeTest extends TypeTestCase
{
    use ValidatorExtensionTrait, EntityTypeExtensionTrait;


    /**
     * Test transction form submitions with valid data expecte success
     *
     * @dataProvider getValidDataProvider
     */
    public function testSubmitValidData($formData)
    {
        $choicesToCompare = new FieldRenovationChoices();
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(FieldRenovationChoicesType::class, $choicesToCompare);

        $choices = new FieldRenovationChoices();

        $choices->setPicturePath($formData['picturePath']);
        $this->assertSame($formData['picturePath'], $choices->getPicturePath());

        $choices->setUuid($formData['uuid']);
        $this->assertSame($formData['uuid'], $choices->getUuid());

        // submit the data to the form directly
        $form->submit($formData);


        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($choices, $choicesToCompare);

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
          'picturePath' => '/path/to/picture1',
          'uuid' => '/uuid1',
        ),];
        yield [array(
          'picturePath' => '/path/to/picture2',
          'uuid' => '/uuid2',
        ),];
    }

}
