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
use Symfony\Component\Form\Form;


class HolidaysTypeTest extends TypeTestCase
{
    /**
     * Test retouch form submitions with valid data expecte success
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
        $holidays->setStartDate($formData['startDate']);
        $holidays->setEndDate($formData['endDate']);
        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertNotEquals($holidays, $holidaysToCompare);

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
        yield [array('title' => 'test', 'startDate' => new \DateTime('2019-01-27'), 'endDate' => new \DateTime('2019-01-29'))];
        yield [array('title' => 'test_2','startDate' => new \DateTime('2019-01-29'), 'endDate' => new \DateTime('2020-01-29'))];
    }
}
