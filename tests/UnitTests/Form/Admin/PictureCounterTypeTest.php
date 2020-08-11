<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Form;

use App\Form\Admin\PictureCounterType;
use App\Entity\PictureCounter;
use App\Entity\PictureCounterPerRetouch;

use Symfony\Component\Form\Test\TypeTestCase;

use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Tests\UnitTests\Form\Traits\ValidatorExtensionTrait;
use Tests\UnitTests\Form\Traits\EntityTypeExtensionTrait;



class PictureCounterTypeTest extends TypeTestCase
{
    use ValidatorExtensionTrait, EntityTypeExtensionTrait;

    private $promoTypes = [
      'user.types.ordinary'=>               'user.types.ordinary',
      'user.types.monthly'=>                'user.types.monthly',
    ];

    protected function getExtensions()
    {
        $type = new PictureCounterType($this->promoTypes);
        // Mock the FormType: entity
        $entityType = new EntityType($this->mockRegistry([]));
        // Mock the FormType: entity
        return array(
            new ValidatorExtension($this->mockValidator()),
            // register the type instances with the PreloadedExtension
            new PreloadedExtension(array($type, 'entity' => $entityType), array()),
        );
    }

    /**
     * Test transction form submitions with valid data expecte success
     *
     * @dataProvider getValidDataProvider
     */
    public function testSubmitValidData($formData)
    {
        $entityToCompare = new PictureCounter();
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(PictureCounterType::class, $entityToCompare);

        $entity = new PictureCounter();

        $entity->setPromoCode($formData['promoCode']);
        $this->assertSame($formData['promoCode'], $entity->getPromoCode());

        $startDate = new \DateTime($formData['startDate']);
        $entity->setStartDate($startDate);
        $this->assertSame($startDate, $entity->getStartDate());

        $entity->setEndDate(new \DateTime($formData['endDate']));
        $this->assertEquals(new \DateTime($formData['endDate']), $entity->getEndDate());

        $entity->setPromoType($formData['promoType']);
        $this->assertSame($formData['promoType'], $entity->getPromoType());

        $entity->setExpired($formData['expired']);
        $this->assertSame($formData['expired'], $entity->getExpired());

        // submit the data to the form directly
        $form->submit($formData);

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
          'promoCode' => '123456',
          'startDate' => "12/12/2012",
          'endDate' => "12/12/2012",
          'promoType' => 'promo.type.assign_to_all_customers',
          'expired' => True,

        ),];
        yield [array(
          'promoCode' => '123457',
          'startDate' => "12/12/2012",
          'endDate' => "12/12/2012",
          'promoType' => 'promo.type.assign_to_all_customers',
          'expired' => True,

        ),];
    }
}
