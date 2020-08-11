<?php

namespace Tests\FunctionalTests\Service;

use App\Entity\Invoice;
use App\Handlers\Extractable\Extractor;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ZipExtractorTests extends WebTestCase
{
    /**
    * @var Extractor
    */
    private $extractor;

    public function setUp()
    {
        $this->extractor = static::createClient()
                                  ->getContainer()
                                  ->get('test.extractor');
    }

    public function getInvoice(int $id)
    {
       $mockInvoice = $this->createMock('App\Entity\Invoice')
                           ->method('getId')
                           ->willReturn($id);
       $mockInvoice->method('getPaymentDate')
                   ->willReturn(new \DateTime());
       return $mockInvoice;
    }

    public function getInvoices()
    {
        $mockInvoices = new ArrayCollection();

        $mockInvoices->add($this->getInvoice(1));
        $mockInvoices->add($this->getInvoice(2));
        $mockInvoices->add($this->getInvoice(3));

        return $mockInvoices;
    }

    public function testCreateArchive()
    {
        $zip = $this->extractor->createArchive('zip', $this->getInvoices(), '/home/hamza', '/home/hamza/Docker/knp-symfony/my_project/public');
        $this->assertNotNull($zip);
    }

    //public function test
}
?>
