<?php

namespace tests\UnitTests\Model;

use App\Entity\Order;

use PHPUnit\Framework\TestCase;


class OrderTest extends TestCase
{

  public function testSettingOrderStatus()
  {
    $order = new Order();

    $order->setOrderStatus(Order::AWAITING_FOR_PAYMENT);
    $this->assertNull($order->getDeliveranceDate());

    $order->setOrderStatus(Order::COMPLETED);
    $this->assertNotNull($order->getDeliveranceDate());

  }

  public function testIsPayed()
  {
    $order = new Order();

    $this->assertFalse($order->isPayed());

    $order->setOrderStatus(Order::AWAITING_FOR_PAYMENT);
    $this->assertTrue($order->isPayed());

  }

  public function testCreationDate()
  {
    $order = $this->getOrder(1);

    $today = new \DateTime('now');
    $this->assertEquals($today->format('Y-m-d'), $order->getCreationDate()->format('Y-m-d'));
  }


  /**
   * @return Order mocked
   */
  public function getOrder()
  {
      $mockOrderEntity = $this->createMock('App\Entity\Order');
      $mockOrderEntity->expects($this->any())->method('getCreationDate')
        ->will($this->returnValue(new \DateTime('now')));

      return $mockOrderEntity;
  }
}
