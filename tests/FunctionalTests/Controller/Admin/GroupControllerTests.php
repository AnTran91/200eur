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
        $crawler = $this->client->request('GET', '/admin/group/');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Liste des groupes")')->count()
        );

        $addButton = $crawler
          ->filter('a:contains("Créer un nouveau groupe")') // find all links with the text "Greet"
          ->eq(0) // select the second link in the list
          ->link()
        ;

        $crawler = $this->client->click($addButton);
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("nom du groupe")')->count()
        );
    }

    public function testNewAction()
    {
      $crawlerCounter = $this->client->request('GET', '/admin/promo/counter/new');

      $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
      $this->assertGreaterThan(
          0, $crawlerCounter->filter('html:contains("Ajouter une nouvelle promotion (avec compteur)")')->count()
      );

      $crawlerDiscount = $this->client->request('GET', '/admin/promo/discount/new');

      $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
      $this->assertGreaterThan(
          0, $crawlerDiscount->filter('html:contains("Ajouter une nouvelle promotion (avec remise)")')->count()
      );
    }

    public function testShowAction()
    {
        $crawler = $this->client->request('GET', '/admin/group/1');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("plus de détails sur le groupe")')->count()
        );

        $editButton = $crawler
          ->filter('a:contains("Modifier")') // find all links with the text "Greet"
          ->eq(0) // select the second link in the list
          ->link()
        ;

        $crawler = $this->client->click($editButton);
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Enregistrer")')->count()
        );
    }

    public function testEditAction()
    {
        $crawler = $this->client->request('GET', '/admin/group/1/edit');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Groupe")')->count()
        );
    }
}
