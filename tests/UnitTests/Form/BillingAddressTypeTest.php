<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Form;

use App\Form\BillingAddressType;
use App\Entity\BillingAddress;

use Symfony\Component\Form\Test\TypeTestCase;

class BillingAddressTypeTest extends TypeTestCase
{
    /**
     * Test billing address form submitions with valid data expecte success
     *
     * @dataProvider getValidDataProvider
     */
    public function testSubmitValidData($formData)
    {
        $billingAddressToCompare = new BillingAddress();
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(BillingAddressType::class, $billingAddressToCompare);

        $billingAddress = new BillingAddress();

        $billingAddress->setFirstName($formData['firstName']);
        $this->assertSame($formData['firstName'], $billingAddress->getFirstName());

        $billingAddress->setLastName($formData['lastName']);
        $this->assertSame($formData['lastName'], $billingAddress->getLastName());

        $billingAddress->setCompany($formData['company']);
        $this->assertSame($formData['company'], $billingAddress->getCompany());

        $billingAddress->setNetworkName($formData['networkName']);
        $this->assertSame($formData['networkName'], $billingAddress->getNetworkName());

        $billingAddress->setAddress($formData['address']);
        $this->assertSame($formData['address'], $billingAddress->getAddress());

        $billingAddress->setSecondaryAddress($formData['secondaryAddress']);
        $this->assertSame($formData['secondaryAddress'], $billingAddress->getSecondaryAddress());

        $billingAddress->setZipCode($formData['zipCode']);
        $this->assertSame($formData['zipCode'], $billingAddress->getZipCode());

        $billingAddress->setCity($formData['city']);
        $this->assertSame($formData['city'], $billingAddress->getCity());

        $billingAddress->setCountry($formData['country']);
        $this->assertSame($formData['country'], $billingAddress->getCountry());

        $billingAddress->setPhone($formData['phone']);
        $this->assertSame($formData['phone'], $billingAddress->getPhone());

        $billingAddress->setCorporateName($formData['corporateName']);
        $this->assertSame($formData['corporateName'], $billingAddress->getCorporateName());

        $billingAddress->setTVA($formData['TVA']);
        $this->assertSame($formData['TVA'], $billingAddress->getTVA());

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($billingAddress, $billingAddressToCompare);

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
          'firstName' => 'test',
          'lastName' => 'test',
          'company' => 'company',
          'networkName' => 'networkName',
          'address'=> 'address',
          'secondaryAddress' => 'TN',
          'zipCode' => '5041',
          'city' => 'test',
          'country' => 'FR',
          'phone' => '123456789',
          'corporateName' => 'test',
          'TVA' => 'test'

        )];
        yield [array(
          'firstName' => 'test',
          'lastName' => 'test',
          'company' => 'company',
          'networkName' => 'networkName',
          'address'=> 'address',
          'secondaryAddress' => 'TN',
          'zipCode' => '5041',
          'city' => 'test',
          'country' => '5041',
          'phone' => '123456789',
          'corporateName' => 'test',
          'TVA' => 'test'
        )];
        yield [array(
          'firstName' => 'test',
          'lastName' => 'test',
          'company' => 'company',
          'networkName' => 'networkName',
          'address'=> 'address',
          'secondaryAddress' => 'TN',
          'zipCode' => '5041',
          'city' => 'test',
          'country' => '5041',
          'phone' => '11111111',
          'corporateName' => 'test',
          'TVA' => 'test'
        )];
    }
}
