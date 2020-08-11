<?php
/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests\Repository;

use App\Entity\Params;
use App\Entity\GroupParams;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ParamRepositoryTest extends KernelTestCase
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

    public function testFindDefaultParams()
    {
      // $group_param = new GroupParams();
      // $group_param->setId(1);$group_param->setTitle("title");
      // $this->entityManager->persist($group_param);
      //
      // $param = new Params();
      // $param->setId(1);$param->setParamKey("titleparam");
      // $param->setGroupParam($group_param);
      // $this->entityManager->persist($param);
      //
      // $param2 = new Params();
      // $param->setId(2);$param->setParamKey("titleparam2");
      // $this->entityManager->persist($param2);
      //
      // $this->entityManager->flush();
      //
      // $params = $this->entityManager->getRepository(Params::class)->findDefaultParams();
      //
      // $this->assertCount(1, $params);


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
