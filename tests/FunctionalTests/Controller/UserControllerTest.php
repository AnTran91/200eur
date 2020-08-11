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
 * User Tests
 *
 * As a user :
 * I can log in
 * I can create an account
 * I can ask to reset my password
 * After logging in I can edit my personal information
 * After logging in I can view my personal information
 */
class UserControllerTest extends WebTestCase
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
     * I can create an account
     */
    public function testRegistration()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        $crawler = $client->request('GET', $this->router->generate('fos_user_registration_register'));
        $this->assertTrue($client->getResponse()->isSuccessful());

        // Fill in the form and submit it
        $form = $crawler->selectButton('_registration')->form(array(
            'fos_user_registration_form[email]'  => 'Test@Teest.com',
            'fos_user_registration_form[username]'  => 'Test',
            'fos_user_registration_form[plainPassword][first]'  => 'Test',
            'fos_user_registration_form[plainPassword][second]' => 'Test'
        ));

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect($this->router->generate('fos_user_registration_confirmed')));
    }

    /**
     * As a user
     * I can log in
     */
    public function testLogInUser()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        $crawler = $client->request('GET', $this->router->generate('fos_user_security_login'));

        $this->assertTrue($client->getResponse()->isSuccessful());
        // Fill in the form and submit it
        $form = $crawler->selectButton('_submit')->form(array(
          '_username'  => 'SimpleUser',
          '_password'  => 'SimpleUser'
        ));

        $client->submit($form);

        $this->assertFalse($client->getResponse()->isRedirect('fos_user_security_login'));
    }

    /**
     * As a user
     * I can ask to reset my password
     */
    public function testResetting()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        $crawler = $client->request('GET', $this->router->generate('fos_user_resetting_request'));

        $this->assertTrue($client->getResponse()->isSuccessful());
        // Fill in the form and submit it
        $form = $crawler->selectButton('_submit')->form(array(
          'username'  => 'SimpleUser@SimpleUser.com'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isRedirect($this->router->generate('fos_user_resetting_check_email', array('username' => 'SimpleUser@SimpleUser.com'))));
    }

    /**
     * As a user
     * After logging in I can edit my personal information
     * After logging in I can view my personal information
     */
    public function testUserProfile()
    {
        // user log in
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'SimpleUser',
            'PHP_AUTH_PW'   => 'SimpleUser',
        ));

        $crawler = $client->request('GET', $this->router->generate('fos_user_profile_edit'));
        // Fill in the form and submit it
        $form = $crawler->selectButton('_update')->form(array(
            'fos_user_profile_form[username]'  => 'SimpleUser',
            'fos_user_profile_form[email]'  => 'TestEdit@Teest.com',
            'fos_user_profile_form[current_password]'  => 'SimpleUser'
        ));

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect($this->router->generate('fos_user_profile_show')));
        $crawler = $client->followRedirect();
        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('html:contains("SimpleUser")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("TestEdit@Teest.com")')->count());
    }

    /**
     * As a user
     *
     * After logging in I can edit my password
     */
    public function testChangePassword()
    {
        // user log in
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'SimpleUser',
            'PHP_AUTH_PW'   => 'SimpleUser',
        ));

        $crawler = $client->request('GET', $this->router->generate('fos_user_change_password'));

        // Fill in the form and submit it
        $form = $crawler->selectButton('_submit')->form(array(
          'fos_user_change_password_form[current_password]'  => 'SimpleUser',
          'fos_user_change_password_form[plainPassword][first]'  => 'TestEdit254',
          'fos_user_change_password_form[plainPassword][second]'  => 'TestEdit254'
      ));
        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect($this->router->generate('fos_user_profile_show')));
    }
}
