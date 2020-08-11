<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Form;

use App\Form\Admin\ProductionType;
use App\Entity\Production;

use Symfony\Component\Form\Test\TypeTestCase;

use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Tests\UnitTests\Form\Traits\ValidatorExtensionTrait;
use Tests\UnitTests\Form\Traits\EntityTypeExtensionTrait;



class ProductionTypeTests extends TypeTestCase
{
    use ValidatorExtensionTrait, EntityTypeExtensionTrait;


    /**
     * Test transction form submitions with valid data expecte success
     *
     * @dataProvider getValidDataProvider
     */
    public function testSubmitValidData($formData)
    {
        $productionToCompare = new Production();
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(ProductionType::class, $productionToCompare);

        $production = new Production();

        $production->setCountry($formData['country']);
        $this->assertSame($formData['country'], $production->getCountry());

        // submit the data to the form directly
        $form->submit($formData);

        $productionToCompare->setCountry($formData['country']);

        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($production, $productionToCompare);

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
        yield [array('country' => 'tunisia',)];
        yield [array('country' => 'test2',)];
    }

}
