<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Form;

use App\Form\Admin\HolidaysType;
use App\Entity\Holidays;

use Symfony\Component\Form\Test\TypeTestCase;

use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Tests\UnitTests\Form\Traits\ValidatorExtensionTrait;
use Tests\UnitTests\Form\Traits\EntityTypeExtensionTrait;



class HolidaysTypeTests extends TypeTestCase
{
    use ValidatorExtensionTrait, EntityTypeExtensionTrait;


    /**
     * Test transction form submitions with valid data expecte success
     *
     * @dataProvider getValidDataProvider
     */
    public function testSubmitValidData($formData)
    {
        $holidaysToCompare = new Holidays();
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(HolidaysType::class, $holidaysToCompare);

        $holidays = new Holidays();

        $holidays->setTitle($formData['title']);
        $this->assertSame($formData['title'], $holidays->getTitle());

        $holidays->setStartDate($formData['startDate']);
        $this->assertSame($formData['startDate'], $holidays->getStartDate());

        $holidays->setEndDate($formData['endDate']);
        $this->assertSame($formData['endDate'], $holidays->getEndDate());

        // submit the data to the form directly
        $form->submit($formData);

        $holidaysToCompare->setTitle($formData['title']);
        $holidaysToCompare->setStartDate($formData['startDate']);
        $holidaysToCompare->setEndDate($formData['endDate']);

        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($holidays, $holidaysToCompare);

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
          'title' => 'test',
          'startDate' => new \DateTime('now'),
          'endDate' => new \DateTime('2018-12-12'),
        ),];
        yield [array(
          'title' => 'test2',
          'startDate' => new \DateTime('now'),
          'endDate' => new \DateTime('2018-12-13'),
        ),];
    }

}
