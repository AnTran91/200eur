<?php
namespace Tests\UnitTests\Form;

use App\Form\PictureRetouchListType;
use App\Form\Type\RetouchEntityType;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Form\Test\TypeTestCase;

use Symfony\Component\Form\PreloadedExtension;

class PictureRetouchListTypeTest extends TypeTestCase
{
    protected function getExtensions()
    {
        // Mock the FormType: entity
        $mockRetouchRepository = $this->getMockBuilder('App\Repository\RetouchRepository')
        ->disableOriginalConstructor()
        ->getMock();

        $mockRetouchRepository->expects($this->any())->method('findByEmmobilierTypeWithFallback')
        ->withAnyParameters()
        ->will($this->returnValue($this->getRetouchsCollection()));

        // Mock the OrderretouchType
        $type = new RetouchEntityType($mockRetouchRepository);

        return array(
          // register the type instances with the PreloadedExtension
          new PreloadedExtension(array($type), array())
      );
    }

    /**
     * Test Group Option form submitions with valid data expecte success
     *
     * @dataProvider getValidDataProvider
     */
    public function testSubmitValidData($formData)
    {
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(PictureRetouchListType::class);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($form->getData(), $formData);
        $this->assertContainsOnly('int', $form->get('retouchs')->getData());

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
        yield [array('retouchs' => ['1', 2 , 3])];
        yield [array('retouchs' => ['2', 1, ])];
    }

    private function getRetouchsCollection(): ArrayCollection
    {
        $retouchCollection  = new ArrayCollection();

        $mockRetouchEntity = $this->getMockBuilder('App\Entity\Retouch')->getMock();
        $mockRetouchEntity->expects($this->any())->method('getId')->will($this->returnValue(1));
        $mockRetouchEntity->expects($this->any())->method('getTitle')->will($this->returnValue('Pertenace'));
        $mockRetouchEntity->expects($this->any())->method('getDescription')->will($this->returnValue(24));
        $mockRetouchEntity->expects($this->any())->method('getPricings')->will($this->returnValue(new ArrayCollection()));


        $retouchCollection->add($mockRetouchEntity);

        $mockRetouchEntity = $this->getMockBuilder('App\Entity\Retouch')->getMock();
        $mockRetouchEntity->expects($this->any())->method('getId')->will($this->returnValue(2));
        $mockRetouchEntity->expects($this->any())->method('getTitle')->will($this->returnValue('Photo Staging 2D'));
        $mockRetouchEntity->expects($this->any())->method('getDescription')->will($this->returnValue(24));
        $mockRetouchEntity->expects($this->any())->method('getPricings')->will($this->returnValue(new ArrayCollection()));

        $retouchCollection->add($mockRetouchEntity);

        $mockRetouchEntity = $this->getMockBuilder('App\Entity\Retouch')->getMock();
        $mockRetouchEntity->expects($this->any())->method('getId')->will($this->returnValue(3));
        $mockRetouchEntity->expects($this->any())->method('getTitle')->will($this->returnValue('Pertenace'));
        $mockRetouchEntity->expects($this->any())->method('getDescription')->will($this->returnValue(24));
        $mockRetouchEntity->expects($this->any())->method('getPricings')->will($this->returnValue(new ArrayCollection()));


        $retouchCollection->add($mockRetouchEntity);

        return $retouchCollection;
    }
}
