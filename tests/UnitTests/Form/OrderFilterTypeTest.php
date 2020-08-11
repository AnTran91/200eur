<?php
namespace Tests\UnitTests\Form;

use App\Form\OrderFilterType;
use App\Form\Type\OrderFilterStatusType;

use Symfony\Component\Form\PreloadedExtension;

use Symfony\Component\Form\Test\TypeTestCase;

class OrderFilterTypeTest extends TypeTestCase
{
    private $orderStatusOptions = [
      'orders.status.in_production' => 'orders.status.in_production',
      'orders.status.awaiting_for_payment' => 'orders.status.awaiting_for_payment',
      'orders.status.awaiting_for_client_response' => 'orders.status.awaiting_for_client_response'
    ];

    protected function getExtensions()
    {
        // create a type instance with the mocked dependencies
        $type = new OrderFilterStatusType($this->orderStatusOptions);

        return array(
            // register the type instances with the PreloadedExtension
            new PreloadedExtension(array($type), array()),
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
        $form = $this->factory->create(OrderFilterType::class);

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
        yield [array('status' => 'orders.status.in_production')];
        yield [array('status' => 'orders.status.awaiting_for_payment')];
        yield [array('status' => 'orders.status.awaiting_for_client_response')];
    }
}
