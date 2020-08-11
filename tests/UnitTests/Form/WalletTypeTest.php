<?php
namespace Tests\UnitTests\Form;

use App\Form\WalletType;
use App\Form\Type\WalletAmountType;

use Symfony\Component\Form\PreloadedExtension;

use Symfony\Component\Form\Form;

use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use Symfony\Component\Form\Test\TypeTestCase;
use Tests\UnitTests\Form\Traits\ValidatorExtensionTrait;

class WalletTypeTest extends TypeTestCase
{
    use ValidatorExtensionTrait;

    protected $walletAmountOptions = [
      'options' => [100, 150 ,200],
      'default' => 100
    ];

    protected $walletThreshold = 250;

    protected function getExtensions()
    {
        // create a type instance with the mocked dependencies
        $walletAmountType = new WalletAmountType($this->walletAmountOptions);
        $walletType = new WalletType($this->walletThreshold);

        return array(
        // register the type instances with the PreloadedExtension
        new ValidatorExtension($this->mockValidator()),
        new PreloadedExtension(array($walletType, $walletAmountType), array()),
    );
    }

    /**
     * Test Group Option form submitions with valid data expecte success
     *
     * @dataProvider getValidDataProvider
     */
    public function testSubmitValidData($formData)
    {
        $form = $this->factory->create(WalletType::class);

        // submit the data to the form directly
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

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
        yield [array('currentAmount' => 10)];
        yield [array('currentAmount' => 200)];
    }
}
