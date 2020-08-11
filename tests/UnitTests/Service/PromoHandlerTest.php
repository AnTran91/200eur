<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\UnitTests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use App\Exception\ValidationException;

use App\Entity\Promo;
use App\Entity\User;

/**
 * Promo Handler Tests
 */
class PromoHandlerTest extends WebTestCase
{
    protected $promoHandler;
    private $user;
    private $errorPromo;
    private $promo;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->promoHandler = static::createClient()
                                    ->getContainer()
                                    ->get('test.promo_handler');

        $this->user = $this->getMockBuilder(User::class)->getMock();
        $this->user->method('getId')
                    ->willReturn(1);

        $this->promo = $this->promoHandler->getPromo('Test-Test');

        $this->errorPromo = $this->promoHandler->getPromo('error-promo');

    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $this->promoHandler = null;
    }

    /**
     * test getPromo method
     */
    public function testGetPromo()
    {
        $promo = $this->promoHandler->getPromo("Test-Test");

        $this->assertEquals($promo->getPromoCode(), "Test-Test");
    }

    /**
     * test validatePromoExist method
     */
    // public function testPromoExistValidate()
    // {
    //     $promo = $this->promoHandler->getPromo("Test-Test");
    //     $this->promoHandler->validatePromoExist($promo);
    //
    //         $this->promoHandler->validatePromoExpiration($promo);
    //         $this->promoHandler->validatePromoDateInterval($promo);
    //         $this->promoHandler->validatePromoUseLimit($promo, $pictureNumber);
    //         $this->promoHandler->validateUserUseLimit($promo, $user, $pictureNumber);
    // }
    //
    // /**
    //  * test validatePromoExist method
    //  */
    // public function testPromoExistValidate()
    // {
    //     $promo = $this->promoHandler->getPromo("Test-Test");
    //     $this->promoHandler->validatePromoExist($promo);
    //
    //         $this->promoHandler->validatePromoExpiration($promo);
    //         $this->promoHandler->validatePromoDateInterval($promo);
    //         $this->promoHandler->validatePromoUseLimit($promo, $pictureNumber);
    //         $this->promoHandler->validateUserUseLimit($promo, $user, $pictureNumber);
    // }

    public function testUserImageLimitThrowsValidationException()
    {
        $this->expectExceptionMessage("Number of pictures reach the limit on this account.");
        $result = $this->promoHandler->validateUserUseLimit($this->errorPromo, $this->user, 2);
    }

    public function testUserImageLimit()
    {
        $result = $this->promoHandler->validateUserUseLimit($this->promo, $this->user, 2);

        $this->assertNotNull($result);
    }

    public function testUserUseLimitThrowsValidationException()
    {
        $this->expectExceptionMessage("Number of use reach the limit on this account.");
        $result = $this->promoHandler->validateUserUseLimit($this->errorPromo, $this->user, 0);
    }

    public function testUserUseLimit()
    {
        $result = $this->promoHandler->validateUserUseLimit($this->promo, $this->user, 0);

        $this->assertNotNull($result);
    }

    public function testValidatePromoExpirationsThrowsValidationException()
    {
        $this->expectExceptionMessage("This promo is expired.");
        $result = $this->promoHandler->validatePromoExpiration($this->errorPromo);

    }

    public function testValidatePromoExpirations()
    {
        $result = $this->promoHandler->validatePromoExpiration($this->promo);
        $this->assertNotNull($result);
    }

    public function testValidatePromoDateIntervalThrowsException()
    {
      $result = $this->promoHandler->validatePromoDateInterval($this->promo);
      $this->assertNotNull($result);
    }

    public function testValidatePromoExistThrowsException()
    {
      $this->expectExceptionMessage("Promo Code does not exist.");
      $promo = $this->promoHandler->getPromo('no-promo');
      $result = $this->promoHandler->validatePromoExist($promo);
    }

    public function testValidatePromoExist()
    {
      $promo = $this->promoHandler->getPromo('Test-Test');
      $result = $this->promoHandler->validatePromoExist($promo);
      $this->assertNotNull($result);
    }

    private function getUser()
    {
        return $this->getMockBuilder(User::class)->getMock()
                    ;
    }
}
