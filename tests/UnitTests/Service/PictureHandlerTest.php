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
use App\Entity\Order;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Picture Handler Tests
 */
class PictureHandlerTest extends WebTestCase
{
    /**
     * @var PictureHandler
     */
    protected $pictureHandler;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->pictureHandler = static::createClient()
                                    ->getContainer()
                                    ->get('test.picture_handler');
    }

    /**
     * Return all  tmp the Uploaded Files
     */
    public function testGetTmpFiles()
    {
        $response = $this->pictureHandler->getTmpFiles([], 'fake-uuid');

        $this->assertEmpty($response);
    }

    /**
     * Return Get order total price
     *
     * @return array
     */
    public function testGetUploadedFilesAndTotalPrice()
    {
        $session = $this->getMockBuilder(SessionInterface::class)->getMock();

        $response = $this->pictureHandler->getUploadedFilesAndTotalPrice($this->getUser(), $session, '24');

        $this->assertArrayHasKey('reduction', $response);
        $this->assertArrayHasKey('total_pictures', $response);
        $this->assertArrayHasKey('total_including_tax_after_reduction', $response);
        $this->assertArrayHasKey('total', $response);
        $this->assertArrayHasKey('uploads', $response);
    }

    /**
     * test user pictures
     */
    public function testGetAllUserPicture()
    {
        $order = $this->getMockBuilder(Order::class)->getMock();

        $order->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($this->getUser()));

        $order->expects($this->any())
            ->method('getUploadFolder')
            ->will($this->returnValue('fake-uuid'));

        $response = $this->pictureHandler->getAllUserPicture([], $order);

        $this->assertInstanceOf(ArrayCollection::class, $response);
    }

    public function getUser()
    {
        $user = $this->getMockBuilder(User::class)->getMock();

        $user->expects($this->any())
          ->method('getUuid')
          ->will($this->returnValue('fake-uuid'));

        return $user;
    }
}
