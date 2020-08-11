<?php
/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\FunctionalTests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;

/**
 * Retouch Tests
 *
 * As a User After logging in :
 * I can view one or all the Retouch
 * I can create an Retouch
 * I can edit an Retouch
 * I can delete an Retouch
 */
class RetouchControllerTest extends WebTestCase
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->router = $kernel->getContainer()
            ->get('router')
        ;
    }

    /**
     * As a user
     * I can create an Retouch
     */
    public function testRetouchCreation()
    {
        // user log in
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'Admin',
            'PHP_AUTH_PW'   => 'Admin',
        ));

        $crawler = $client->request('GET', $this->router->generate('retouch_add'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * As a user
     * I can edit an Retouch
     */
    /*public function testRetouchUpdate()
    {
        // user log in
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'Admin',
            'PHP_AUTH_PW'   => 'Admin',
        ));

        $crawler = $client->request('GET', $this->router->generate('retouch_edit', ['id' => 1]));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * As a user
     * I can delete an Retouch
     */
    /*public function testRetouchDelete()
    {
        // user log in
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'Admin',
            'PHP_AUTH_PW'   => 'Admin',
        ));

        $crawler = $client->request('DELETE', $this->router->generate('retouch_delete', ['id' => 1]));
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for DELETE {}");
    }

    /**
     * As a user
     * I can view one or all the Retouch
     */
    public function testRetouchList()
    {
        // user log in
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'Admin',
            'PHP_AUTH_PW'   => 'Admin',
        ));

        $client->request('GET', $this->router->generate('retouch_index'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    // public function testCompleteScenario()
    // {
    //     // user log in
    //     $client = static::createClient(array(), array(
    //         'PHP_AUTH_USER' => 'Admin',
    //         'PHP_AUTH_PW'   => 'Admin',
    //     ));
    //
    //     // Create a new entry in the database
    //     $crawler = $client->request('GET', $this->router->generate('retouch_index'));
    //     $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /{{ retouch_index }}/");
    //     $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
    //
    //     // Fill in the form and submit it
    //     $form = $crawler->selectButton('Create')->form(array(
    //         '{{ form_type_name|lower }}[field_name]'  => 'Test',
    //         // ... other fields to fill
    //     ));
    //
    //     $client->submit($form);
    //     $crawler = $client->followRedirect();
    //
    //     // Check data in the show view
    //     $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');
    //
    //     // Edit the entity
    //     $crawler = $client->click($crawler->selectLink('Edit')->link());
    //
    //     $form = $crawler->selectButton('Update')->form(array(
    //         '{{ form_type_name|lower }}[field_name]'  => 'Foo',
    //         // ... other fields to fill
    //     ));
    //
    //     $client->submit($form);
    //     $crawler = $client->followRedirect();
    //
    //     // Check the element contains an attribute with value equals "Foo"
    //     $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');
    //
    //     // Delete the entity
    //     $client->submit($crawler->selectButton('Delete')->form());
    //     $crawler = $client->followRedirect();
    //
    //     // Check the entity has been delete on the list
    //     $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
    // }
}
