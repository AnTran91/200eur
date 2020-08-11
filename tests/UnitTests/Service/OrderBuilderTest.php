<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\UnitTests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use App\Entity\User;
use App\Entity\Transaction;
use App\Entity\Order;

/**
 * Transaction Handler Tests
 */
class TransactionHandlerTests extends WebTestCase
{
    /**
     * @var TransactionHandler
     */
    protected $builder;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->builder = static::createClient()
                                    ->getContainer()
                                    ->get('test.builder');
        $this->builder->reset();
    }

    /**
     * test User Set
     *
     * @depand testOrderCreation()
     */
    public function testUserSet()
    {
        $this->assertNotNull($this->builder->getOrder());

        $user = new User();
        $this->builder->setUser($user);

        $this->assertEquals($this->builder->getOrder()->getClient(), $user);
    }

    /**
     * test Transaction Set
     *
     * @depand testOrderCreation()
     */
    public function testTransactionSet()
    {
        $this->assertNotNull($this->builder->getOrder());

        $transaction = new Transaction();
        $this->builder->setTransaction($transaction);

        $this->assertEquals($this->builder->getOrder()->getTransaction(), $transaction);
    }

    /**
     * test order status
     *
     * @depand testOrderCreation()
     */
    public function testOrderStatus()
    {
        $this->assertNotNull($this->builder->getOrder());

        $this->builder->setOrderStatus();

        $this->assertEquals($this->builder->getOrder()->getOrderStatus(), Order::AWAITING_FOR_PAYMENT);

        $this->builder->setOrderInProduction();
        $today = new \DateTime('now');
        $this->assertEquals($this->builder->getOrder()->getPaymentDate()->format('Y-m-d'), $today->format('Y-m-d'));
    }

    // /**
    //  * test comment set
    //  *
    //  * @depand testOrderCreation()
    //  */
    // public function testCommentSet()
    // {
    //     $this->assertNotNull($this->builder->getOrder());
    //
    //     $comment = "test";
    //     $this->builder->setComment($comment);
    //     $this->assertEquals($this->builder->getOrder()->getComment(), $comment);
    // }
    //
    // /**
    //  * test order time Set
    //  *
    //  * @depand testOrderCreation()
    //  */
    // public function testTimeSet()
    // {
    //     $this->assertNotNull($this->builder->getOrder());
    //
    //     $time = "24";
    //     $this->builder->setOrderTime($time);
    //     $this->assertEquals($this->builder->getOrder()->getTime(), $time);
    // }
    //
    // /**
    //  * test upload folder Set
    //  *
    //  * @depand testOrderCreation()
    //  */
    // public function testUploadFolderSet()
    // {
    //     $this->assertNotNull($this->builder->getOrder());
    //
    //     $uploadFolder = "testFolder";
    //     $this->builder->setUploadFolder($uploadFolder);
    //     $this->assertEquals($this->builder->getOrder()->getUploadFolder(), $uploadFolder);
    // }
    //
    // /**
    //  * test promo with fake data
    //  *
    //  * @depand testOrderCreation()
    //  */
    // public function testPromoWithFakeData()
    // {
    //     $this->assertNotNull($this->builder->getOrder());
    //
    //     $promoCode = "FakeCode";
    //     $this->builder->setPromo($promoCode);
    //     $this->assertNull($this->builder->getOrder()->getPromo());
    // }
    //
    // /**
    //  * test promo with fake data
    //  *
    //  * @depand testOrderCreation()
    //  */
    // public function testPromoWithRealData()
    // {
    //     $this->assertNotNull($this->builder->getOrder());
    //
    //     $promoCode = "Test-Test";
    //     $this->builder->setPromo($promoCode);
    //     $this->assertNotNull($this->builder->getOrder()->getPromo());
    //     $this->assertEquals($this->builder->getOrder()->getPromo()->getPromoCode(), $promoCode);
    // }
    //
    // /**
    //  * test Inovice
    //  *
    //  * @depand testOrderCreation()
    //  */
    // public function testInovice()
    // {
    //     $this->assertNotNull($this->builder->getOrder());
    //
    //     $this->builder->generateInovice();
    //     $this->assertNotNull($this->builder->getOrder()->getInvoice());
    // }
    //
    // private function getUser()
    // {
    //     return $this->getMockBuilder(User::class)->getMock();
    // }
    //
    // private function getTransaction()
    // {
    //     return $this->getMockBuilder(Transaction::class)->getMock();
    // }
}
