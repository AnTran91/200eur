<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Form;

use App\Form\Admin\NetworkType;
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



class NetworkTypeTests extends TypeTestCase
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
    public function testSubmitValidData($formData, User $owner, Agency $agency, User $employee)
    {
        $networkToCompare = new Network();
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(NetworkType::class, $networkToCompare);

        $network = new Network();

        $network->setName($formData['name']);
        $this->assertSame($formData['name'], $network->getName());

        $network->setRegistrationCode($formData['registrationCode']);
        $this->assertSame($formData['registrationCode'], $network->getRegistrationCode());

        $network->addAgency($agency);
        $this->assertContains($agency, $network->getAgencies());

        $network->setOwner($owner);
        $this->assertSame($owner, $network->getOwner());

        $network->addEmployee($employee);
        $this->assertContains($employee, $network->getEmployees());

        // submit the data to the form directly
        $form->submit($formData);

        $networkToCompare->addAgency($agency);
        $networkToCompare->setOwner($owner);
        $networkToCompare->addEmployee($employee);

        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($network, $networkToCompare);

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
          'registrationCode' => 'test'
        ), $this->getUser(1), $this->getAgency(10), $this->getUser(34)];
        yield [array(
          'name' => 'lnpi',
          'registrationCode' => 'regislnpi'
        ), $this->getUser(2), $this->getAgency(30), $this->getUser(25)];
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
     * @return User mocked
     */
    public function getUser(int $id)
    {
        $mockUserEntity = $this->createMock('App\Entity\User');
        $mockUserEntity->expects($this->any())->method('getId')
          ->will($this->returnValue($id));

        return $mockUserEntity;
    }

    /**
     * @return Agency mocked
     */
    public function getAgency(int $id)
    {
        $mockAgencyEntity = $this->createMock('App\Entity\Agency');
        $mockAgencyEntity->expects($this->any())->method('getId')
          ->will($this->returnValue($id));

        return $mockAgencyEntity;
    }
}
