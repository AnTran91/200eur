<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Form;

use App\Form\Admin\FileUploadType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;

use Tests\UnitTests\Form\Traits\ValidatorExtensionTrait;

use Symfony\Component\Form\Test\TypeTestCase;

class FileUploadTypeTest extends TypeTestCase
{
    use ValidatorExtensionTrait;

    protected $validation = [
      'allowedMimeTypes' => ['application/octet-stream','image/*'],
      'allowedExtensions' => ['jpeg','jpg','gif'],
      'sizeLimit' => 200000000, # 200M
      'chunkSize' => 2000000, # 2M
      'itemLimit' => 50,
    ];
    protected $file;
    protected $image;
    protected $base64;

    public function setUp()
    {
        $this->file = tempnam(sys_get_temp_dir(), 'upl'); // create file
        imagepng(imagecreatetruecolor(10, 10), $this->file); // create and write image/png to it
        $this->base64 = sprintf('data:image/%s;base64,%s', pathinfo($this->file, PATHINFO_EXTENSION), base64_encode(file_get_contents($this->file)));
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
     * Test transction form submitions with valid data expecte success
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
          'metadata' => array(
            'uiid' => '7b1aa81d-73ca-4372-974a-b71ccadaf7a4'
          ),
          'file' => $this->file,
          'base64' => $this->base64
        )];
        yield [array(
          'metadata' => array(
            'uiid' => '7b1aa81d-73ca-4372-974a-b71ccadaf7a4'
          ),
          'file' => $this->file,
          'base64' => $this->base64
        )];
    }

    public function tearDown()
    {
        unlink($this->file);
    }
}
