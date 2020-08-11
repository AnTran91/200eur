<?php

namespace tests\FunctionalTests\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DashboardControllerTests extends WebTestCase
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
        $crawler = $this->client->request('GET', '/admin/dashboard' );

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Tableau de bord")')->count()
        );

        $clientSpaceButton = $crawler
          ->filter('a:contains("Espace client")') // find all links with the text "Greet"
          ->eq(0) // select the second link in the list
          ->link()
        ;

        $crawler = $this->client->click($clientSpaceButton);
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Historique des Commandes")')->count()
        );
    }

}
