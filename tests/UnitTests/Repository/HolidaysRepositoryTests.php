<?php
/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Repository;

use App\Entity\Holidays;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HolidaysRepositoryTest extends KernelTestCase
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

    public function testFindByDay()
    {
        $date = new \DateTime('2018-07-25');
        $holidays = $this->entityManager->getRepository(Holidays::class)->findByDay($date);

        $this->assertSame($holidays->getTitle(), 'Fête de la Républic');

    }

    public function testFindByMonthInterval()
    {
        $month = 1;
        $holidays = $this->entityManager->getRepository(Holidays::class)->find($month);

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
