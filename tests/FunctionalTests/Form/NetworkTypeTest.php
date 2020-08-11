<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Form;

use App\Form\Admin\NetworkType;
use App\Entity\Network;
use Symfony\Component\Form\Test\TypeTestCase;

use Symfony\Component\Form\PreloadedExtension;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadata;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class NetworkTypeTest extends TypeTestCase
{
      protected function getExtensions()
      {
          // Mock the FormType: entity
          $mockEntityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
          ->disableOriginalConstructor()
          ->getMock();

          $mockRegistry = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
          ->disableOriginalConstructor()
          ->getMock();

          $mockRegistry->expects($this->any())->method('getManagerForClass')
          ->will($this->returnValue($mockEntityManager));

          $mockEntityManager->expects($this->any())->method('getClassMetadata')
          ->withAnyParameters()
          ->will($this->returnValue(new ClassMetadata('entity')));

          $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
          ->disableOriginalConstructor()
          ->getMock();

          $mockEntityManager->expects($this->any())->method('getRepository')
          ->withAnyParameters()
          ->will($this->returnValue($repo));

          $repo->expects($this->any())->method('findAll')
          ->withAnyParameters()
          ->will($this->returnValue(new ArrayCollection()));


          $entityType = new EntityType($mockRegistry);

          return array(
              // register the type instances with the PreloadedExtension
              new PreloadedExtension(array('entity' => $entityType), array())
          );
      }

    /**
     * Test retouch form submitions with valid data expecte success
     *
     * @dataProvider getValidDataProvider
     */
    public function testSubmitValidData($formData)
    {
        $formData = array(
             'name' => 'test',
             'registrationCode' => 'test2',
         );

         $networkToCompare = new Network();
         // $objectToCompare will retrieve data from the form submission; pass it as the second argument
         $form = $this->factory->create(NetworkType::class, $networkToCompare);

         $network = new Network();
         // ...populate $object properties with the data stored in $formData
         $network->setName($formData['name']);
         $network->setRegistrationCode($formData['registrationCode']);

         // submit the data to the form directly
         $form->submit($formData);

         $this->assertTrue($form->isSynchronized());

         // check that $objectToCompare was modified as expected when the form was submitted
         $this->assertEquals($network, $networkToCompare);

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
        yield [array('name' => 'test', 'registrationCode' => '123456',)];
        yield [array('name' => 'test', 'registrationCode' => '123457',)];
        yield [array('name' => 'test', 'registrationCode' => '123458',)];
    }
}
