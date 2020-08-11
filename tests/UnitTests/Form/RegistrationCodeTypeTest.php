<?php
namespace Tests\UnitTests\Form;

use App\Form\RegistrationFormType;
use App\Form\Shared\Type\BillingAddressType;
use App\Form\Shared\Type\OrganizationSelectorType;

use App\Repository\OrganizationRepository;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use App\Entity\User;
use App\Entity\BillingAddress;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;

use Symfony\Component\Form\PreloadedExtension;

use Symfony\Component\Form\Form;

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

use App\Form\Shared\DataTransformer\RegistrationCodeToOrganizationTransformer;
use Beelab\Recaptcha2Bundle\Form\Type\RecaptchaType;

use Symfony\Component\Form\Test\TypeTestCase;
use Tests\UnitTests\Form\Traits\ValidatorExtensionTrait;

class RegistrationCodeTypeTest extends TypeTestCase
{
    use ValidatorExtensionTrait;

    protected $siteKey = 'test';

    protected function getExtensions()
    {
        // create a type instance with the mocked dependencies

        $mockOrganizationRepository = $this->createMock(OrganizationRepository::class);

        $mockOrganizationRepository->expects($this->once())->method('findOneBy')
        ->withAnyParameters()
        ->will($this->returnValue($this->getOrganization()));

        $transformer = new RegistrationCodeToOrganizationTransformer($mockOrganizationRepository);

        $billingAddressType = new BillingAddressType();
        $organizationSelectorType = new OrganizationSelectorType($transformer);
        $registrationFormType = new BaseRegistrationFormType(User::class);

        $recaptchaType = new RecaptchaType($this->siteKey);

        return array(
          // register the type instances with the PreloadedExtension
          new ValidatorExtension($this->mockValidator()),
          new PreloadedExtension(array($recaptchaType, $organizationSelectorType, $registrationFormType, $billingAddressType), array()),
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
        $form = $this->factory->create(RegistrationFormType::class, $userToCompare);

        $user = new User();

        $user->setEmail($formData['email']);
        $this->assertSame($formData['email'], $user->getEmail());

        $user->setEmailSecondary($formData['emailSecondary']);
        $this->assertSame($formData['emailSecondary'], $user->getEmailSecondary());

        $user->setReceiveNewsletter($formData['receiveNewsletter']);
        $this->assertSame($formData['receiveNewsletter'], $user->getReceiveNewsletter());

        $user->setReceiveTargetedEmailsFromPromotion($formData['receiveTargetedEmailsFromPromotion']);
        $this->assertSame($formData['receiveTargetedEmailsFromPromotion'], $user->getReceiveTargetedEmailsFromPromotion());

        $user->setPlainPassword($formData['plainPassword']['first']);
        $this->assertSame($formData['plainPassword']['first'], $user->getPlainPassword());

        $user->setOrganization($this->getOrganization());

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
        'email' => 'test@test.com',
        'plainPassword' => [
          'first' => 'test',
          'second' => 'test'
        ],
        'receiveNewsletter' => true,
        'organization' => 'test',
        'receiveTargetedEmailsFromPromotion' => true,
        'emailSecondary' => 'test@test.com',
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

        yield [array(
      'email' => 'test@test.com',
      'plainPassword' => [
        'first' => 123,
        'second' => 123
      ],
      'receiveNewsletter' => true,
      'receiveTargetedEmailsFromPromotion' => true,
      'organization' => 'test',
      'emailSecondary' => null,
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

    /**
     * @return Organization mocked object
     */
    public function getOrganization()
    {
        $mockPromoEntity = $this->createMock('App\Entity\Organization');
        $mockPromoEntity->method('getId')
          ->will($this->returnValue(1));
        $mockPromoEntity->method('getRegistrationCode')
          ->will($this->returnValue('test'));

        return $mockPromoEntity;
    }
}
