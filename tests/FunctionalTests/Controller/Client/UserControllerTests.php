<?php

namespace tests\FunctionalTests\Controller\Client;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderControllerTests extends WebTestCase
{
    protected $client;

    public function setUp()
    {
        $this->client = static::createClient(array(), array(
          'PHP_AUTH_USER' => 'admin@admin.com',
          'PHP_AUTH_PW'   => 'admin',
        ));
    }

    public function testShowAction()
    {
        $crawler = $this->client->request('GET', '/profile/' );

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Mon Compte")')->count()
        );
    }


    public function testEditAction()
    {
        $crawler = $this->client->request('GET', '/profile/edit' );

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Sauvegarder les modifications")')->count()
        );

        $changePasswordButton = $crawler
            ->filter('a:contains("Changer le mot de passe")') // find all links with the text "Greet"
            ->eq(0) // select the second link in the list
            ->link()
        ;

        $crawler = $this->client->click($changePasswordButton);
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Changer le mot de passe de votre compte")')->count()
        );

        $walletButton = $crawler
            ->filter('#js-wallet-show-modal') // find all links with the text "Greet"
            ->link()
        ;

        $crawler = $this->client->click($walletButton);
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Changer le mot de passe de votre compte")')->count()
        );
    }


    public function testChangePasswordAction()
    {
        $crawler = $this->client->request('GET', 'profile/change-password' );

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Changer le mot de passe de votre compte")')->count()
        );
    }

    // array('/profile/'),
    // array('/profile/edit'),
    // array('profile/change-password'),
}
