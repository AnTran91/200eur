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
        $crawler = $this->client->request('GET', '/admin/order/' );

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Liste des Commandes")')->count()
        );
    }

    public function testShowAction()
    {
        $crawler = $this->client->request('GET', 'orders/details/160' );

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Récapitulatif de votre commande")')->count()
        );
    }


    public function testEditAction()
    {
        // $crawler = $this->client->request('GET', '/create/step/upload' );
        //
        // $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        // $this->assertGreaterThan(
        //     0, $crawler->filter('html:contains("Téléchargez vos images")')->count()
        // );
    }

}
