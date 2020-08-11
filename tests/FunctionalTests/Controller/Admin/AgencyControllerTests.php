<?php

namespace Tests\FunctionalTests\Controller\Admin;

use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AgencyControllerTests extends WebTestCase
{
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient(array(), array(
          'PHP_AUTH_USER' => 'mhana.hamza.tn@gmail.com',
          'PHP_AUTH_PW'   => 'netha81892',
        ));
    }

    public function testIndexAction()
    {
        $crawler = $this->client->request('GET', '/admin/agency/');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Liste des Agences")')->count()
        );

        $addButton = $crawler
          ->filter('a:contains("Ajouter")') // find all links with the text "Greet"
          ->eq(0) // select the second link in the list
          ->link()
        ;

        $crawler = $this->client->click($addButton);
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Nouvelle Agence")')->count()
        );
    }

    public function testAddAction()
    {
        $crawler = $this->client->request('GET', '/admin/agency/new');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Nouvelle Agence")')->count()
        );

        $backButton = $crawler
          ->filter('a:contains("Retour")') // find all links with the text "Greet"
          ->eq(0) // select the second link in the list
          ->link()
        ;

        $crawler = $this->client->click($backButton);
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Liste des Agences")')->count()
        );
    }

    public function testShowAction()
    {
        $crawler = $this->client->request('GET', '/admin/agency/3');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("agence")')->count()
        );

        $addButton = $crawler
          ->filter('a:contains("Modifier")') // find all links with the text "Greet"
          ->eq(0) // select the second link in the list
          ->link()
        ;

        $crawler = $this->client->click($addButton);
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Modification d\'une Agence")')->count()
        );
    }
}
