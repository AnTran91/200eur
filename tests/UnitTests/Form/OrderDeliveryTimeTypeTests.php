<?php
namespace Tests\UnitTests\Form;

use App\Form\OrderDeliveryTimeType;

use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Translation\TranslatorInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Forms;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadata;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Tests\UnitTests\Form\Traits\EntityTypeExtensionTrait;

class OrderDeliveryTimeTypeTests extends TypeTestCase
{
    use EntityTypeExtensionTrait;

    private $translator;

    protected function setUp()
    {
        // mock any dependencies
        $this->translator = $this->createMock(TranslatorInterface::class);
        parent::setUp();
    }

    protected function getExtensions()
    {
        // Mock the OrderDeliveryTimeType
        $type = new OrderDeliveryTimeType($this->translator);
        // Mock the FormType: entity
        $entityType = new EntityType($this->mockRegistry($this->getDeliveryTimeCollection(), 'deliveryTime'));

        return array(
            // register the type instances with the PreloadedExtension
            new PreloadedExtension(array($type, 'deliveryTime' => $entityType), array())
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
        $form = $this->factory->create(OrderDeliveryTimeType::class);

        // submit the data to the form directly
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertInstanceOf('App\Entity\OrderDeliveryTime', $form->get('deliveryTime')->getData());

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
        yield [array('deliveryTime' => 0)];
        yield [array('deliveryTime' => 1)];
    }

    private function getDeliveryTimeCollection()
    {
        $deliveryTimeCollection  = new ArrayCollection();

        $mockOrderDeliveryTimeEntity = $this->getMockBuilder('App\Entity\OrderDeliveryTime')->getMock();
        $mockOrderDeliveryTimeEntity->expects($this->any())->method('getId')
        ->will($this->returnValue(10));
        $mockOrderDeliveryTimeEntity->expects($this->any())->method('getUnit')
        ->will($this->returnValue('time.hour'));
        $mockOrderDeliveryTimeEntity->expects($this->any())->method('getTime')
        ->will($this->returnValue(24));

        $deliveryTimeCollection->add($mockOrderDeliveryTimeEntity);

        $mockOrderDeliveryTimeEntity = $this->getMockBuilder('App\Entity\OrderDeliveryTime')->getMock();
        $mockOrderDeliveryTimeEntity->expects($this->any())->method('getId')
        ->will($this->returnValue(20));
        $mockOrderDeliveryTimeEntity->expects($this->any())->method('getUnit')
        ->will($this->returnValue('time.hour'));
        $mockOrderDeliveryTimeEntity->expects($this->any())->method('getTime')
        ->will($this->returnValue(48));

        $deliveryTimeCollection->add($mockOrderDeliveryTimeEntity);

        return $deliveryTimeCollection;
    }
}
