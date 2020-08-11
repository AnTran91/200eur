<?php
namespace Tests\UnitTests\Form;

use App\Form\FileUploadType;

use Symfony\Component\Form\PreloadedExtension;

use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Component\Validator\ConstraintViolationList;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\Form\Test\TypeTestCase;

use Tests\UnitTests\Form\Traits\ValidatorExtensionTrait;

class FileUploadTypeTests extends TypeTestCase
{
    use ValidatorExtensionTrait;

    protected $file;
    protected $image;

    protected $validation = [
      'allowedMimeTypes' => ['application/octet-stream','image/*'],
      'allowedExtensions' => ['jpeg','jpg','gif'],
      'sizeLimit' => 200000000, # 200M
      'chunkSize' => 2000000, # 2M
      'itemLimit' => 50,
    ];

    public function setUp()
    {
        $this->file = tempnam(sys_get_temp_dir(), 'upl'); // create file
        imagepng(imagecreatetruecolor(10, 10), $this->file); // create and write image/png to it
        $this->image = new UploadedFile(
            $this->file,
            'new_image.png'
        );

        parent::setUp();
    }

    protected function getExtensions()
    {
        // create a type instance with the mocked dependencies
        $type = new FileUploadType($this->validation);

        return array(
            // register the type instances with the PreloadedExtension
            new ValidatorExtension($this->mockValidator()),
            new PreloadedExtension(array($type), array()),
        );
    }

    /**
     * Test file upload form submitions with valid data expecte success.
     *
     * @dataProvider getValidDataProvider
     */
    public function testSubmitValidData($formData)
    {
        $form = $this->factory->create(FileUploadType::class);
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
        yield [array(
          'qquuid' => 'dummy',
          'qqfilename'  => 'dummy.jpg',
          'qqtotalfilesize' => '10025',
          'qqfile' => $this->image,
          'qqtotalparts' => '1',
          'qqchunksize' => '2000000',
          'qqpartbyteoffset' => '10025',
          'qqpartindex' => '1'
        )];
        yield [array(
          'qquuid' => 'dummy',
          'qqfilename'  => 'dummy.jpg',
          'qqtotalfilesize' => '500000000',
          'qqfile' => $this->image,
          'qqtotalparts' => '20',
          'qqchunksize' => '2000000',
          'qqpartbyteoffset' => '10025',
          'qqpartindex' => '20'
        )];
        yield [array(
          'qquuid' => 'dummy',
          'qqfilename'  => 'dummy.jpg',
          'qqtotalfilesize' => '500000000',
          'qqfile' => $this->image,
          'qqtotalparts' => '20',
          'qqchunksize' => '2000000',
          'qqpartbyteoffset' => '2000000',
          'qqpartindex' => '11'
        )];
    }

    public function tearDown()
    {
        unlink($this->file);
    }
}
