<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\UnitTests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use App\Entity\Transaction;

/**
 * Transaction Handler Tests
 */
class TransactionHandlerTests extends WebTestCase
{
    /**
     * @var TransactionHandler
     */
    protected $transHandler;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->transHandler = static::createClient()
                                    ->getContainer()
                                    ->get('test.transaction_service');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $this->transHandler = null;
    }

    /**
     * test transaction
     */
    public function testTransactionExcpectToSuccess()
    {
        $transaction = $this->getTransaction();
        $this->transHandler->transaction($transaction);

        $this->assertTrue($this->transHandler->isSuccessful());
        $this->assertNotNull($this->transHandler->getComment());

        $response = $this->transHandler->getResponse();

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('NUMTRANS', $response);
        $this->assertArrayHasKey('NUMAPPEL', $response);
        $this->assertArrayHasKey('AUTORISATION', $response);
        $this->assertArrayHasKey('NUMQUESTION', $response);

        $this->assertArrayHasKey('CODEREPONSE', $response);
        $this->assertArrayHasKey('COMMENTAIRE', $response);

        $this->assertEquals($response['COMMENTAIRE'], $this->transHandler->getComment());
        $this->assertEquals(0, $response['CODEREPONSE']);

        $transaction->setTransactionResponse($response);

        $this->assertEquals($response['NUMTRANS'], $transaction->getTransactionNumber());
        $this->assertEquals($response['NUMAPPEL'], $transaction->getCallNumber());
        $this->assertEquals($response['AUTORISATION'], $transaction->getAuthorizationCode());
        $this->assertEquals($response['NUMQUESTION'], $transaction->getQuestionNumber());
    }

    /**
     * test transaction
     *
     * @dataProvider getFakeData
     */
    public function testTransactionExcpectToFail($transaction)
    {
        $this->transHandler->transaction($transaction);

        $this->assertFalse($this->transHandler->isSuccessful());

        $response = $this->transHandler->getResponse();

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('NUMTRANS', $response);
        $this->assertArrayHasKey('NUMAPPEL', $response);
        $this->assertArrayHasKey('AUTORISATION', $response);
        $this->assertArrayHasKey('NUMQUESTION', $response);

        $this->assertArrayHasKey('CODEREPONSE', $response);
        $this->assertArrayHasKey('COMMENTAIRE', $response);

        $this->assertEquals($response['COMMENTAIRE'], $this->transHandler->getComment());
        $this->assertNotEquals(0, $response['CODEREPONSE']);
    }

    /**
     * Get transaction object
     */
    private function getTransaction()
    {
        return (new Transaction())
              ->setAmount(100)
              ->setReference('test@test.com')
              ->setCardNumber('1111222233334444')
              ->setExpiryYear('2020')
              ->setExpiryMonth('02')
              ->setCsc(123)
              ->setCardType('CB')
              ;
    }

    /**
     * Return Different Sets of Data
     *
     * @return array
     */
    public function getFakeData()
    {
        yield [
          (new Transaction())
              ->setAmount(100)
              ->setReference('test@test.com')
              ->setCardNumber('2154546545852265655846')
              ->setExpiryYear('2020')
              ->setExpiryMonth('02')
              ->setCsc(123)
              ->setCardType('CB')
        ];
        yield [
          (new Transaction())
              ->setAmount(100)
              ->setReference('test@test.com')
              ->setCardNumber('1111222233334444')
              ->setExpiryYear('2001')
              ->setExpiryMonth('02')
              ->setCsc(123)
              ->setCardType('CB')
        ];
        yield [
          (new Transaction())
              ->setAmount(100)
              ->setReference('test@test.com')
              ->setCardNumber('1111222233334444')
              ->setExpiryYear('200001')
              ->setExpiryMonth('02')
              ->setCsc(123)
              ->setCardType('CB')
        ];
        yield [
          (new Transaction())
              ->setAmount(100)
              ->setReference('test@test.com')
              ->setCardNumber('1111222233334444')
              ->setExpiryYear('2020')
              ->setExpiryMonth('0205225')
              ->setCsc(123)
              ->setCardType('CB')
        ];
        yield [
          (new Transaction())
              ->setAmount(100)
              ->setReference('test@test.com')
              ->setCardNumber('1111222233334444')
              ->setExpiryYear('2020')
              ->setExpiryMonth('01')
              ->setCsc(1230455512)
              ->setCardType('CB')
        ];
        yield [
          (new Transaction())
              ->setAmount(120)
              ->setReference('test@test.com')
              ->setCardNumber('Fake')
              ->setExpiryYear('Fake')
              ->setExpiryMonth('Fake')
              ->setCsc(12351825)
              ->setCardType('Fake')
        ];
    }
}
