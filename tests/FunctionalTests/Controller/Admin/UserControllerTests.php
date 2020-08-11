<?php

namespace Tests\FunctionalTests\Controller\Admin;

use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class InvoiceControllerTests extends WebTestCase
{
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient(array(), array(
          'PHP_AUTH_USER' => 'admin@admin.com',
          'PHP_AUTH_PW'   => 'admin',
        ));
    }

    public function testIndexAction()
    {
        $crawler = $this->client->request('GET', '/admin/user/');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Liste des Utilisateurs")')->count()
        );

        $addButton = $crawler
          ->filter('a:contains("Ajouter")') // find all links with the text "Greet"
          ->eq(0) // select the second link in the list
          ->link()
        ;

        $crawler = $this->client->click($addButton);
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Créer un Nouveau Utilisateur")')->count()
        );
    }

    public function testNewAction()
    {
      $crawlerCounter = $this->client->request('GET', '/admin/user/new');

      $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
      $this->assertGreaterThan(
          0, $crawlerCounter->filter('html:contains("Créer un Nouveau Utilisateur")')->count()
      );
    }

    public function testShowAction()
    {
        $crawler = $this->client->request('GET', '/admin/user/8');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Dernière connexion")')->count()
        );

        $editButton = $crawler
          ->filter('a:contains("Modifier")') // find all links with the text "Greet"
          ->eq(0) // select the second link in the list
          ->link()
        ;

        $crawler = $this->client->click($editButton);
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Modifier un Utilisateur")')->count()
        );
    }

    public function testEditAction()
    {
        $crawler = $this->client->request('GET', '/admin/user/8/edit');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Modifier un Utilisateur")')->count()
        );
    }
}
