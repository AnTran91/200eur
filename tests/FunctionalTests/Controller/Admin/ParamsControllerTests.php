<?php

namespace Tests\FunctionalTests\Controller\Admin;

use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ParamsControllerTests extends WebTestCase
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
        $crawler = $this->client->request('GET', '/admin/field/group/');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Liste des Paramètres")')->count()
        );
    }

    public function testAddAction()
    {
        $crawler = $this->client->request('GET', '/admin/field/group/new');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Nouveau Paramètre")')->count()
        );

        $backButton = $crawler
          ->filter('a:contains("Retour")') // find all links with the text "Greet"
          ->eq(0) // select the second link in the list
          ->link()
        ;

        $crawler = $this->client->click($backButton);
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Liste des Paramètres")')->count()
        );
    }

    public function testEditAction()
    {
        $crawler = $this->client->request('GET', '/admin/field/group/1/edit');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Modification de Paramètres")')->count()
        );
    }
}
