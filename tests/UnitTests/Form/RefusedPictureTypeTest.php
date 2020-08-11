<?php
namespace Tests\UnitTests\Form;

use App\Form\RefusedPictureType;
use App\Entity\Picture;
use Symfony\Component\Form\Test\TypeTestCase;

class RefusedPictureTypeTest extends TypeTestCase
{
    /**
     * Test Group Option form submitions with valid data expecte success
     *
     * @dataProvider getValidDataProvider
     */
    public function testSubmitValidData($formData)
    {
        $pictureToCompare = new Picture();
        $form = $this->factory->create(RefusedPictureType::class, $pictureToCompare);

        $picture = new picture();

        $picture->setStatus($formData['status']);
        $this->assertSame($formData['status'], $picture->getStatus());

        $picture->setCommentary($formData['commentary']);
        $this->assertSame($formData['commentary'], $picture->getCommentary());

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($picture, $pictureToCompare);

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
        yield [array('status' => Picture::REFUSED, 'commentary' => 'Test commentary')];
        yield [array('status' => Picture::VALIDATED, 'commentary' => 'Test commentary')];
        yield [array('status' => Picture::AWAITING_FOR_VERIFICATION, 'commentary' => 'Test commentary')];
    }
}
