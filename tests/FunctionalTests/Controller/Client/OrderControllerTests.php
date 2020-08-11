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

    public function testIndexAction()
    {
        $crawler = $this->client->request('GET', '/orders/' );

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Historique des Commandes")')->count()
        );
        $newOrderButton = $crawler
            ->filter('a:contains("Nouvelle commande")') // find all links with the text "Greet"
            ->eq(0) // select the second link in the list
            ->link()
        ;

        $crawler = $this->client->click($newOrderButton);
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Téléchargez vos images")')->count()
        );
    }


    public function testOrderDetailsAction()
    {
        // $crawler = $this->client->request('GET', 'orders/details/id', ['id' => 1] );
        //
        // $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        // $this->assertGreaterThan(
        //     0, $crawler->filter('html:contains("Récapitulatif de votre commande")')->count()
        // );
    }


    public function testFirstStepCreateAction()
    {
        $crawler = $this->client->request('GET', '/create/step/upload' );

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Téléchargez vos images")')->count()
        );
    }

}
