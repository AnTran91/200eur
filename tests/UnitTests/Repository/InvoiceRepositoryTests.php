<?php
/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Repository;

use App\Entity\Invoice;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InvoiceRepositoryTest extends KernelTestCase
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

    public function testFindByRegistrationCode()
    {
        $registrationCode = 'aloalo';
        $agency = $this->entityManager->getRepository(Agency::class)->findByRegistrationCode($registrationCode);

        $this->assertSame($agency->getName(), 'Alo');


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
