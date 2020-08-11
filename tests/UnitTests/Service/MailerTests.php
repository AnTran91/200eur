<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\UnitTests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Order;

/**
* Mailer Tests
*/

class MailerTests extends WebTestCase{

  /**
   * @var MailerHandler
   */
  protected $mailerHandler;

  /**
   * @var Order
   */
  protected $order;

  /**
   * @var ObjectManager
   */
  private $objectManager;


  public function __construct(ObjectManager $objectManager)
  {
      $this->objectManager = $objectManager;
  }

  /**
   * {@inheritDoc}
   */
  public function setUp()
  {
      $this->mailerHandler = static::createClient()
                                  ->getContainer()
                                  ->get('test.mailer_handler');
      $this->order = $this->createMock(Order::class);
      $this->order->method('getOrderNumber')
                  ->willReturn(1);
      $this->order->method('getId')
                  ->willReturn(1);

  }

  public function testOnWalletThresholdMail()
  {
    [$body, $title] = $this->mailerHandler->WalletThresholdEvent();

    $this->assertContains('Solde Tirelire', $title);
    $this->assertContains('le montant de votre Tirelire est moins de 50euros', $body);

  }

  public function testOnCreationMail()
  {
    [$body, $title] = $this->mailerHandler->CreationEvent($this->order);

    $this->assertContains('Emmobilier-Création de la Commande '.$this->order->getOrderNumber(), $title);
    $this->assertContains('a été crée avec succès !', $body);

  }

  public function testOnFirstDeadLineMail()
  {
    [$body, $title] = $this->mailerHandler->FirstDeadlineReadyEvent($this->order);

    $this->assertContains('Photos du 1er délais prêtes pour la commande '.$this->order->getOrderNumber(), $title);
    $this->assertContains('La Production vous informe que les images du *délai court* ont été déposées', $body);

  }

  public function testOnReadyMail()
  {
    [$body, $title] = $this->mailerHandler->ReadyEvent($this->order);

    $this->assertContains("Commande ".$this->order->getOrderNumber()." Prête", $title);
    $this->assertContains("N°".$this->order->getOrderNumber()."</a></strong> ont été déposées sur votre Compte Personnel", $body);

  }

  public function testOnCanceledMail()
  {
    [$body, $title] = $this->mailerHandler->CanceledEvent($this->order);

    $this->assertContains("Commande ".$this->order->getorderNumber()." annulée!", $title);
    $this->assertContains("N°".$this->order->getOrderNumber()."</a></strong> a été annulée !", $body);

  }

  public function testOnStopedMail()
  {
    [$body, $title] = $this->mailerHandler->StopedEvent($this->order);

    $this->assertContains("Commande ".$this->order->getorderNumber()." mise en Arrêt", $title);
    $this->assertContains("N°".$this->order->getOrderNumber()." est mise en arrèt", $body);

  }

  public function testOnPendingMail()
  {
    [$body, $title] = $this->mailerHandler->PendingEvent($this->order);

    $this->assertContains("MISE EN ATTENTE de votre commande N°".$this->order->getorderNumber().".", $title);
    $this->assertContains("a été mise en attente", $body);

  }

  // public function testSendMail()
  // {
  //
  //   $orderRepository = $this->objectManager
  //       ->getRepository(OrderCreation::class);
  //   $order = $orderRepository->find(1);
  //   
  //   $client = static::createClient();
  //
  //   // enables the profiler for the next request (it does nothing if the profiler is not available)
  //   $client->enableProfiler();
  //
  //   $crawler = $client->request('POST', '/order/now_with_wallet/{id}', ['order' => $order]);
  //
  //   $mailCollector = $client->getProfile()->getCollector('swiftmailer');
  //
  //   // checks that an email was sent
  //   $this->assertSame(1, $mailCollector->getMessageCount());
  // }
}
