<?php
/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Repository;

use App\Entity\Order;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testFindByPromoCodeAndUser()
    {
        $order = $this->entityManager->getRepository(Order::class)
                                    ->findByPromoCodeAndUser("Test-Test", 1);
        $this->assertCount(2, $order);

        // $order = $this->entityManager->getRepository(OrderCreation::class)
        //                             ->findByPromoCodeAndUser("Test-Test", 1);
        // $this->assertCount(0, $order);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}
