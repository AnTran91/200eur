<?php

namespace Tests\FunctionalTests\Controller\Admin;

use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TransactionControllerTests extends WebTestCase
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
        $crawler = $this->client->request('GET', '/admin/transaction/');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Liste des Transactions")')->count()
        );
    }


    public function testShowAction()
    {
        $crawler = $this->client->request('GET', '/admin/transaction/15');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0, $crawler->filter('html:contains("Information-transfer transaction")')->count()
        );
    }
}
