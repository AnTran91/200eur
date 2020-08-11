<?php
namespace Tests\UnitTests\Form;

use App\Form\PromoCodeType;
use App\Form\Type\PromoSelectorType;

use Symfony\Component\Form\PreloadedExtension;

use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use App\Form\DataTransformer\CouponCodeToPromoTransformer;
use App\Repository\PromoRepository;

use Symfony\Component\Form\Form;

use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\GroupOptions;
use Symfony\Component\Form\Test\TypeTestCase;
use Tests\UnitTests\Form\Traits\ValidatorExtensionTrait;

class PromoCodeTypeTest extends TypeTestCase
{
    use ValidatorExtensionTrait;

    protected function getExtensions()
    {
        // create a type instance with the mocked dependencies
        $mockPromoRepository = $this->createMock(PromoRepository::class);

        $mockPromoRepository->expects($this->any())->method('findOneBy')
        ->withAnyParameters()
        ->will($this->returnValue($this->getPromo()));

        $transformer = new CouponCodeToPromoTransformer($mockPromoRepository);
        $promoCodeType = new PromoSelectorType($transformer);

        return array(
        // register the type instances with the PreloadedExtension
        new ValidatorExtension($this->mockValidator()),
        new PreloadedExtension(array($promoCodeType), array()),
    );
    }

    /**
     * Test Group Option form submitions with valid data expecte success
     *
     * @dataProvider getValidDataProvider
     */
    public function testSubmitValidData($formData)
    {
        $form = $this->factory->create(PromoCodeType::class);
        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertInstanceOf('App\Entity\Promo', $form->get('promoCode')->getData());

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
        yield [array('promoCode' => 'test')];
    }

    /**
     * @return Promo mocked object
     */
    public function getPromo()
    {
        $mockPromoEntity = $this->createMock('App\Entity\Promo');
        $mockPromoEntity->method('getId')
        ->will($this->returnValue(1));
        $mockPromoEntity->method('getPromoCode')
        ->will($this->returnValue('test'));

        return $mockPromoEntity;
    }
}
