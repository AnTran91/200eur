<?php
namespace Tests\UnitTests\Form;

use App\Form\ProfileFormType;
use App\Form\BillingAddressType;

use Symfony\Component\Form\Form;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use App\Entity\User;
use App\Entity\BillingAddress;

use FOS\UserBundle\Form\Type\ProfileFormType as BaseProfileFormType;

use Symfony\Component\Form\PreloadedExtension;

use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use Vich\UploaderBundle\Form\Type\VichImageType;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Vich\UploaderBundle\Handler\UploadHandler;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;
use Vich\UploaderBundle\Storage\StorageInterface;

use App\Form\Type\LanguageType;

use Symfony\Component\Form\Test\TypeTestCase;

use Tests\UnitTests\Form\Traits\ValidatorExtensionTrait;

class ProfileFormTypeTest extends TypeTestCase
{
    use ValidatorExtensionTrait;

    protected $languageOptions = [
      "language.en" => "en",
      "language.fr" => "fr"
    ];

    protected $file;
    protected $image;

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
        $billingAddressType = new BillingAddressType();
        $profileFormType = new BaseProfileFormType(User::class);

        $storage = $this->createMock(StorageInterface::class);
        $storage
            ->expects($this->any())
            ->method('resolvePath')
            ->will($this->returnValue('resolved-path'));
        $uploadHandler = $this->createMock(UploadHandler::class);
        $propertyMappingFactory = $this->createMock(PropertyMappingFactory::class);
        $propertyAccessor = $this->createMock(PropertyAccessor::class);

        $vichImageType = new VichImageType($storage, $uploadHandler, $propertyMappingFactory, $propertyAccessor);
        $languageType = new LanguageType($this->languageOptions);

        return array(
          // register the type instances with the PreloadedExtension
          new ValidatorExtension($this->mockValidator()),
          new PreloadedExtension(array($profileFormType, $billingAddressType, $vichImageType, $languageType), array()),
      );
    }

    /**
     * Test user form submitions with valid data expecte success
     *
     * @dataProvider getValidDataProvider
     */
    public function testSubmitValidData($formData)
    {
        $userToCompare = new User();
        $form = $this->factory->create(ProfileFormType::class, $userToCompare);

        $user = new User();

        $user->setImageFile($formData['imageFile']);
        $this->assertSame($formData['imageFile'], $user->getImageFile());

        $user->setFirstName($formData['firstName']);
        $this->assertSame($formData['firstName'], $user->getFirstName());

        $user->setLastName($formData['lastName']);
        $this->assertSame($formData['lastName'], $user->getLastName());

        $user->setEmailSecondary($formData['emailSecondary']);
        $this->assertSame($formData['emailSecondary'], $user->getEmailSecondary());

        $user->setLanguage($formData['language']);
        $this->assertSame($formData['language'], $user->getLanguage());

        $user->getBillingAddress()->setFirstName($formData['billingAddress']['firstName']);
        $this->assertSame($formData['billingAddress']['firstName'], $user->getBillingAddress()->getFirstName());

        $user->getBillingAddress()->setLastName($formData['billingAddress']['lastName']);
        $this->assertSame($formData['billingAddress']['lastName'], $user->getBillingAddress()->getLastName());

        $user->getBillingAddress()->setCompany($formData['billingAddress']['company']);
        $this->assertSame($formData['billingAddress']['company'], $user->getBillingAddress()->getCompany());

        $user->getBillingAddress()->setNetworkName($formData['billingAddress']['networkName']);
        $this->assertSame($formData['billingAddress']['networkName'], $user->getBillingAddress()->getNetworkName());

        $user->getBillingAddress()->setAddress($formData['billingAddress']['address']);
        $this->assertSame($formData['billingAddress']['address'], $user->getBillingAddress()->getAddress());

        $user->getBillingAddress()->setSecondaryAddress($formData['billingAddress']['secondaryAddress']);
        $this->assertSame($formData['billingAddress']['secondaryAddress'], $user->getBillingAddress()->getSecondaryAddress());

        $user->getBillingAddress()->setZipCode($formData['billingAddress']['zipCode']);
        $this->assertSame($formData['billingAddress']['zipCode'], $user->getBillingAddress()->getZipCode());

        $user->getBillingAddress()->setCity($formData['billingAddress']['city']);
        $this->assertSame($formData['billingAddress']['city'], $user->getBillingAddress()->getCity());

        $user->getBillingAddress()->setCountry($formData['billingAddress']['country']);
        $this->assertSame($formData['billingAddress']['country'], $user->getBillingAddress()->getCountry());

        $user->getBillingAddress()->setPhone($formData['billingAddress']['phone']);
        $this->assertSame($formData['billingAddress']['phone'], $user->getBillingAddress()->getPhone());

        $user->getBillingAddress()->setCorporateName($formData['billingAddress']['corporateName']);
        $this->assertSame($formData['billingAddress']['corporateName'], $user->getBillingAddress()->getCorporateName());

        $user->getBillingAddress()->setTVA($formData['billingAddress']['TVA']);
        $this->assertSame($formData['billingAddress']['TVA'], $user->getBillingAddress()->getTVA());

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($user, $userToCompare);

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
        'imageFile' => $this->image,
        'firstName' => 'test',
        'lastName' => 'test',
        'emailSecondary' => 'test@test.com',
        'language'=> 'fr',
        'billingAddress' => array(
          'firstName' => 'test',
          'lastName' => 'test',
          'company' => 'company',
          'networkName' => 'networkName',
          'address'=> 'address',
          'secondaryAddress' => 'TN',
          'zipCode' => '5041',
          'city' => 'test',
          'country' => 'FR',
          'phone' => '12345678',
          'corporateName' => 'test',
          'TVA' => 'test'
        )
      )];
    }

    public function tearDown()
    {
        unlink($this->file);
    }
}
