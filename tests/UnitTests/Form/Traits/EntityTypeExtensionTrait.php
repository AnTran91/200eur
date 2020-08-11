<?php

namespace Tests\UnitTests\Form\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadata;

trait EntityTypeExtensionTrait
{
    /**
     * @return ManagerRegistry
     */
    public function mockRegistry($result, $classMetadata = 'entity')
    {
        $mockEntityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
          ->disableOriginalConstructor()
          ->getMock();

        $mockRegistry = $this->getMockBuilder('Doctrine\Common\Persistence\ManagerRegistry')
          ->disableOriginalConstructor()
          ->getMock();

        $mockRegistry->expects($this->any())->method('getManagerForClass')
          ->will($this->returnValue($mockEntityManager));

        $mockEntityManager ->expects($this->any())->method('getClassMetadata')
          ->withAnyParameters()
          ->will($this->returnValue(new ClassMetadata($classMetadata)));

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
          ->disableOriginalConstructor()
          ->getMock();

        $mockEntityManager ->expects($this->any())->method('getRepository')
          ->withAnyParameters()
          ->will($this->returnValue($repo));

        $mockQueryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
          ->setMethods(array('where', 'getQuery', 'orderBy', 'getSQL', 'execute'))
          ->setConstructorArgs(array($mockEntityManager))
          ->getMock();

        $repo->expects($this->any())->method('createQueryBuilder')
          ->withAnyParameters()
          ->will($this->returnValue($mockQueryBuilder));

        $mockQueryBuilder->expects($this->any())->method('where')
          ->withAnyParameters()
          ->will($this->returnSelf());

        $mockQueryBuilder->expects($this->any())->method('orderBy')
          ->withAnyParameters()
          ->will($this->returnSelf());

        $mockQueryBuilder->expects($this->any())->method('getQuery')
          ->withAnyParameters()
          ->will($this->returnSelf());

        $mockQueryBuilder->expects($this->any())->method('getSQL')
          ->withAnyParameters()
          ->will($this->returnSelf());

        $mockQueryBuilder->expects($this->any())->method('execute')
          ->withAnyParameters()
          ->will($this->returnValue($result));

        return $mockRegistry;
    }
}
