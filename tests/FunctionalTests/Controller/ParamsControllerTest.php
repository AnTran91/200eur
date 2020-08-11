<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Repository;


use App\Entity\Params;
use App\Controller\ParamController;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ParamsTest extends WebTestCase
{
    protected $router;

    public function setUp()
    {
      $kernel = static::createKernel();
      $kernel->boot();

      $this->router = $kernel->getContainer()
          ->get('router')
      ;
    }

    public function testAddAction()
    {
      $client = static::createClient(array(), array(
          'PHP_AUTH_USER' => 'testuser',
          'PHP_AUTH_PW'   => 'testpass',
      ));

      $crawler = $client->request('GET', $this->router->generate('param_add'));
      $this->assertTrue($client->getResponse()->isSuccessful());

      // Fill in the form and submit it
      $form = $crawler->selectButton('Valider')->form(array(
          'param[paramKey]'=> 'test',
      ));

      $client->submit($form);
      $this->assertTrue($client->getResponse()->isRedirect($this->router->generate('param_list')));
    }

    public function testEditAction()
    {
      $client = static::createClient(array(), array(
          'PHP_AUTH_USER' => 'testuser',
          'PHP_AUTH_PW'   => 'testpass',
      ));

      $crawler = $client->request('GET', $this->router->generate('param_edit', ['id'=> '4']));
      $this->assertTrue($client->getResponse()->isSuccessful());

      // Fill in the form and submit it
      $form = $crawler->selectButton('Valider')->form(array(
          'param[paramKey]'=> 'font',
      ));

      $client->submit($form);
      $this->assertTrue($client->getResponse()->isRedirect($this->router->generate('param_list')));
    }
}
