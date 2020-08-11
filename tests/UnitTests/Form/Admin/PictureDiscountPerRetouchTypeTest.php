<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Form;

use App\Form\Admin\PictureDiscountPerRetouchType;
use App\Entity\PictureDiscountPerRetouch;

use Symfony\Component\Form\Test\TypeTestCase;

use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Tests\UnitTests\Form\Traits\ValidatorExtensionTrait;
use Tests\UnitTests\Form\Traits\EntityTypeExtensionTrait;



class PictureDiscountPerRetouchTypeTest extends TypeTestCase
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
        $entityToCompare = new PictureDiscountPerRetouch();
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(PictureDiscountPerRetouchType::class, $entityToCompare);

        $entity = new PictureDiscountPerRetouch();

        $entity->setReduction($formData['reduction']);
        $this->assertSame($formData['reduction'], $entity->getReduction());

        $entity->setImageLimitPerUser($formData['imageLimitPerUser']);
        $this->assertSame($formData['imageLimitPerUser'], $entity->getImageLimitPerUser());

        $entity->setImageLimit($formData['imageLimit']);
        $this->assertSame($formData['imageLimit'], $entity->getImageLimit());

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
          'reduction' => 10,
          'imageLimitPerUser' => 2,
          'imageLimit' => 10,
          'retouch' => 0
        ), $this->getRetouch(1)];
        yield [array(
          'reduction' => 15,
          'imageLimitPerUser' => 3,
          'imageLimit' => 20,
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
