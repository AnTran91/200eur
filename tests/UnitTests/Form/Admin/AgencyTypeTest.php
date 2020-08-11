<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Form;

use App\Form\Admin\AgencyType;
use App\Entity\Agency;
use App\Entity\User;
use App\Entity\Network;

use Symfony\Component\Form\Test\TypeTestCase;

use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Tests\UnitTests\Form\Traits\ValidatorExtensionTrait;
use Tests\UnitTests\Form\Traits\EntityTypeExtensionTrait;

class AgencyTypeTest extends TypeTestCase
{
    use ValidatorExtensionTrait, EntityTypeExtensionTrait;

    protected function getExtensions()
    {
        // Mock the FormType: entity
        $entityType = new EntityType($this->mockRegistry($this->getOwners()));

        return array(
            new ValidatorExtension($this->mockValidator()),
            // register the type instances with the PreloadedExtension
            new PreloadedExtension(array('entity' => $entityType), array()),
        );
    }

    /**
     * Test transction form submitions with valid data expecte success
     *
     * @dataProvider getValidDataProvider
     */
    public function testSubmitValidData($formData, User $owner, Network $network, User $employee)
    {
        $agencyToCompare = new Agency();
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(AgencyType::class, $agencyToCompare);

        $agency = new Agency();

        $agency->setName($formData['name']);
        $this->assertSame($formData['name'], $agency->getName());

        $agency->setPhone($formData['phone']);
        $this->assertSame($formData['phone'], $agency->getPhone());

        $agency->setAddress($formData['address']);
        $this->assertSame($formData['address'], $agency->getAddress());

        $agency->setRegistrationCode($formData['registrationCode']);
        $this->assertSame($formData['registrationCode'], $agency->getRegistrationCode());

        $agency->setNetwork($network);
        $this->assertSame($network, $agency->getNetwork());

        $agency->setOwner($owner);
        $this->assertSame($owner, $agency->getOwner());

        $agency->addEmployee($employee);
        $this->assertContains($employee, $agency->getEmployees());

        // submit the data to the form directly
        $form->submit($formData);

        $agencyToCompare->setNetwork($network);
        $agencyToCompare->setOwner($owner);
        $agencyToCompare->addEmployee($employee);

        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($agency, $agencyToCompare);

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
          'name' => 'test',
          'phone' => '12345678',
          'address' => 'test adrress',
          'registrationCode' => 'test'
        ), $this->getUser(1), $this->getNetwork(10), $this->getUser(34)];
        yield [array(
          'name' => null,
          'phone' => null,
          'address' => null,
          'registrationCode' => null
        ), $this->getUser(2), $this->getNetwork(30), $this->getUser(25)];
    }



    /**
     * @return User[] mocked user
     */
    public function getOwners()
    {
        $userCollection  = new ArrayCollection();

        $userCollection->add($this->getUser(1));
        $userCollection->add($this->getUser(2));
        $userCollection->add($this->getUser(3));

        return $userCollection;
    }

    /**
     * @return User[] mocked user
     */
    public function getEmployees()
    {
        $userCollection  = new ArrayCollection();

        $userCollection->add($this->getUser(25));
        $userCollection->add($this->getUser(34));
        $userCollection->add($this->getUser(43));

        return $userCollection;
    }

    /**
     * @return Network[] mocked user
     */
    public function getNetworks()
    {
        $userCollection  = new ArrayCollection();

        $userCollection->add($this->getNetwork(10));
        $userCollection->add($this->getNetwork(20));
        $userCollection->add($this->getNetwork(30));

        return $userCollection;
    }

    /**
     * @return User mocked user
     */
    public function getUser(int $id)
    {
        $mockUserEntity = $this->createMock('App\Entity\User');
        $mockUserEntity->expects($this->any())->method('getId')
          ->will($this->returnValue($id));

        return $mockUserEntity;
    }

    /**
     * @return Network mocked user
     */
    public function getNetwork(int $id)
    {
        $mockNetworkEntity = $this->createMock('App\Entity\Network');
        $mockNetworkEntity->expects($this->any())->method('getId')
          ->will($this->returnValue($id));

        return $mockNetworkEntity;
    }
}
