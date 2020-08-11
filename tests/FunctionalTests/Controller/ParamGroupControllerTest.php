<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Repository;


use App\Entity\GroupParams;
use App\Controller\GroupParamController;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GroupParamTest extends WebTestCase
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

      $crawler = $client->request('GET', $this->router->generate('group_param_add'));
      $this->assertTrue($client->getResponse()->isSuccessful());

      // Fill in the form and submit it
      $form = $crawler->selectButton('Valider')->form(array(
        'group_param[title]'=> 'my_params',
        'group_param[font]'=> 'aria',
        'group_param[color]'=> 'red',
        'group_param[format]'=> 'jpeg'
      ));

      $client->submit($form);
      $this->assertTrue($client->getResponse()->isRedirect($this->router->generate('group_param_list')));

      $form_error = $crawler->selectButton('Valider')->form(array(
        'group_param[title]'=> 'my_params',
        'group_param[font]'=> 'aria',
        'group_param[color]'=> 'yellowish',
        'group_param[format]'=> 'tiff'
      ));

      $client->submit($form_error);
      $this->expectException(InvalidArgumentException::class);
    }

    public function testEditAction()
    {
      $client = static::createClient(array(), array(
          'PHP_AUTH_USER' => 'testuser',
          'PHP_AUTH_PW'   => 'testpass',
      ));

      $crawler = $client->request('GET', $this->router->generate('group_param_edit', ['id'=> '17']));
      $this->assertTrue($client->getResponse()->isSuccessful());

      // Fill in the form and submit it
      $form = $crawler->selectButton('Valider')->form(array(
        'group_param[title]'=> 'my_params',
        'group_param[font]'=> 'aria',
        'group_param[color]'=> 'red',
        'group_param[format]'=> 'tiff'
      ));

      $client->submit($form);
      $this->assertTrue($client->getResponse()->isRedirect($this->router->generate('group_param_list')));
    }
}
