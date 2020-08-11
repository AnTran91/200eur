<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Form;

use App\Form\Admin\PictureCounterPerRetouchType;
use App\Entity\PictureCounterPerRetouch;

use Symfony\Component\Form\Test\TypeTestCase;

use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Tests\UnitTests\Form\Traits\ValidatorExtensionTrait;
use Tests\UnitTests\Form\Traits\EntityTypeExtensionTrait;



class PictureCounterPerRetouchTypeTest extends TypeTestCase
{
    use ValidatorExtensionTrait, EntityTypeExtensionTrait;

    protected function getExtensions()
    {
        // Mock the FormType: entity
        $entityType = new EntityType($this->mockRegistry($this->getRetouches()));

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
    public function testSubmitValidData($formData, $retouch)
    {
        $entityToCompare = new PictureCounterPerRetouch();
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(PictureCounterPerRetouchType::class, $entityToCompare);

        $entity = new PictureCounterPerRetouch();

        $entity->setImageCounterLimit($formData['imageCounterLimit']);
        $this->assertSame($formData['imageCounterLimit'], $entity->getImageCounterLimit());

        $entity->setImageCounterLimitWithReduction($formData['imageCounterLimitWithReduction']);
        $this->assertSame($formData['imageCounterLimitWithReduction'], $entity->getImageCounterLimitWithReduction());

        $entity->setRetouch($retouch);
        $this->assertSame($retouch, $entity->getRetouch());

        // submit the data to the form directly
        $form->submit($formData);

        //$entityToCompare->setRetouch($retouch);

        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($entity, $entityToCompare);

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
          'imageCounterLimit' => 10,
          'imageCounterLimitWithReduction' => 2,
          'retouch' => 0
        ), $this->getRetouch(1)];
        yield [array(
          'imageCounterLimit' => 15,
          'imageCounterLimitWithReduction' => 3,
          'retouch' => 1
        ), $this->getRetouch(2)];
    }


    /**
     * @return Retouches[] mocked retouches
     */
    public function getRetouches()
    {
        $retouchCollection  = new ArrayCollection();

        $retouchCollection->add($this->getRetouch(1));
        $retouchCollection->add($this->getRetouch(2));
        $retouchCollection->add($this->getRetouch(3));

        return $retouchCollection;
    }

    /**
     * @return Retouch mocked
     */
    public function getRetouch(int $id)
    {
        $mockRetouchEntity = $this->createMock('App\Entity\Retouch');
        $mockRetouchEntity->expects($this->any())->method('getId')
          ->will($this->returnValue($id));

        return $mockRetouchEntity;
    }
}
