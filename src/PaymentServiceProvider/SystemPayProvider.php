<?php

/**
 * (c) Sfari Rami <rami2sfari@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\PaymentServiceProvider;

use App\Entity\Transaction;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * The SystemPayProvider handle payment by system pay form
 */
class SystemPayProvider implements SystemPayProviderInterface
{
	use SystemPayTrait;
	
    /**
     * @var array
     */
    private $mandatoryFields;

    /**
     * @var string
     */
    private $key;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
	
	/**
	 * @var TranslatorInterface
	 */
	private $translator;
	
	/**
	 * Constructor
	 *
	 * @param EntityManagerInterface    $em
	 * @param TranslatorInterface       $translator
	 * @param array                     $systemPayConfigs
	 */
    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator, array $systemPayConfigs)
    {
        $this->entityManager = $em;
        $this->translator = $translator;
        $this->mandatoryFields = $systemPayConfigs;

        $this->setCurrentKey();
    }

    /**
     * {@inheritDoc}
     */
    public function prepareTransaction(array $options = array()): void
    {
        $transaction = (new Transaction())
              ->setAmount($options['amount'])
              ->setTransactionNumber($this->getLastTransactionNumber())
              ->setTransactionExtNumber($this->getExtTransactionNumber())
              ->setClient($options['customer'])
              ->setReference($options['reference'])
        ;
        $this->entityManager->persist($transaction);
        $this->entityManager->flush();


        $this->mandatoryFields = array_merge([
          'amount' => (100 * $transaction->getAmount()),
          'trans_id' => $transaction->getTransactionNumber(),
          'order_info' => $transaction->getReference(),
          'order_info2' => $transaction->getTransactionExtNumber(),
          'trans_date' => $transaction->getDate()->format('YmdHis'),

          'order_id' => $options['orderID'],
          'url_return' => $options['validationURL'],
          'shop_url' => $options['shopURL']
        ], $this->getCustomerInfo($transaction->getClient()), $this->mandatoryFields);
    }

    /**
     * {@inheritDoc}
     */
    public function getRequest(): array
    {
        $this->mandatoryFields['signature'] = $this->getSignature();

        return $this->mandatoryFields;
    }
	
	/**
	 * {@inheritDoc}
	 */
    public function responseHandler(array $query, string $locale): array
    {
        // get transaction
        $transaction = $this->findTransaction($query);

        if ($this->validateSignature($query) && !is_null($transaction) && !$transaction->isPaid()) {

            if ('00' === $query['vads_result'] && Transaction::AUTHORISED == $query['vads_trans_status']) {
                $transaction->setPaid(true);
            }
            // update transaction entity
            $transaction->setStatus($query['vads_trans_status'])
                ->setCardNumber($query['vads_card_number'])
                ->setCardBrand($query['vads_card_brand'])
                ->setLogResponse($query);

            $this->entityManager->flush();

            return [
              'VALIDATION' => $transaction->isPaid(),
              'TRANSACTION' => $transaction,
	          'STATUS' => $query['vads_trans_status'] ?? Transaction::ABANDONED,
              // messages
              'COMMENT' => $this->getTransactionStatus($query['vads_trans_status'], $locale),
              'WARRANTY' => $this->getTransactionWarranty($query['vads_warranty_result'], $locale),
              'DELAY_PAYMENT' => $this->getTransactionDelay($query['vads_capture_delay'], $locale),
            ];
        }

        return [
          'VALIDATION' => false,
          'TRANSACTION' => $transaction,
	      'STATUS' => $query['vads_trans_status'] ?? Transaction::ABANDONED,
          // messages
          'COMMENT' => $this->getInvalidSignatureMessage($locale)
        ];
    }
}
