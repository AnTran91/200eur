<?php

/**
 * (c) Sfari Rami <rami2sfari@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\PaymentServiceProvider;

use App\Entity\Transaction;
use App\Entity\User;
use Symfony\Component\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SystemPayTrait
 *
 * @property array                  $mandatoryFields
 * @property TranslatorInterface    $translator
 * @property string                 $key
 * @property EntityManagerInterface $entityManager
 */
trait SystemPayTrait
{
	/**
	 * Set prefix to fields
	 *
	 * @param array $fields
	 * @return array
	 */
	public function setPrefixToFields(array $fields): array
	{
		$newTab = array();
		foreach ($fields as $field => $value) {
			$newTab[sprintf('vads_%s', $field)] = $value;
		}
		return $newTab;
	}
	
	/**
	 * Set the certificate depending on the ctx_mode setting, TEST or PRODUCTION.
	 *
	 * @return void
	 */
	public function setCurrentKey():void
	{
		$this->key = $this->mandatoryFields['key_TEST'];
		
		if ($this->mandatoryFields['ctx_mode'] === 'PRODUCTION') {
			$this->key = $this->mandatoryFields['key_PROD'];
		}
		// remove the Sensitive data
		unset($this->mandatoryFields['key_PROD']);
		unset($this->mandatoryFields['key_TEST']);
	}
	
	/**
	 * When computing the signature, the fields must be UTF8 encoded. The same applies to your shop which must send all the parameters in UTF8 encoding to the payment gateway.
	 * If the fields are not sent to the gateway in UTF8 encoding, then you will observe special characters in the form especially with the accents.
	 *
	 * @param array $query
	 *
	 * @return bool
	 */
	public function validateSignature(array $query): bool
	{
		if (!empty($query['signature'])) {
			$signature = $query['signature'];
			unset($query['signature']);
			if ($signature == $this->getSignature($query)) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Get the signature
	 *
	 * @param array $fields
	 * @return string
	 */
	public function getSignature(array $fields = array()): string
	{
		if (!$fields) {
			$fields = $this->mandatoryFields = $this->setPrefixToFields($this->mandatoryFields);
		}
		ksort($fields);
		$contenuSignature = "";
		foreach ($fields as $field => $value) {
			$contenuSignature .= $value."+";
		}
		$contenuSignature .= $this->key;
		$signature = sha1($contenuSignature);
		return $signature;
	}
	
	/**
	 * @param array $query
	 *
	 * @return Transaction
	 */
	public function findTransaction(array $query): ?Transaction
	{
		return $this->entityManager
			->getRepository(Transaction::class)
			->findOneBy(['transactionNumber' => $query['vads_trans_id'], 'reference' => $query['vads_order_info'], 'transactionExtNumber' => $query['vads_order_info2']]);
	}
	
	/**
	 * The transaction ID must absolutely be set between 000000 and 899999.
	 *
	 * @example
	 * 23 is not a valid transaction ID, if the transaction is lower than 100000 you have to add the corresponding number of ZEROS before the number.
	 * 000023 is a valid transaction ID.
	 *
	 * @return string
	 */
	public function getLastTransactionNumber(): string
	{
		$lastTransactionNumber = (int) $this->entityManager
			->getRepository(Transaction::class)
			->findLastTransactionNumber()['lastTransactionNumber'];
		
		if ($lastTransactionNumber > 0 && 899999 > $lastTransactionNumber) {
			$lastTransactionNumber += 1;
		} else {
			$lastTransactionNumber = 1;
		}
		
		return sprintf("%06d", $lastTransactionNumber);
	}
	
	/**
	 * Extra reference of the transaction.
	 *
	 *
	 * @example
	 * Universally unique identifier
	 *
	 * @return string
	 */
	public function getExtTransactionNumber(): string
	{
		while (true) {
			$uuid = \App\Utils\UUID::v5();
			
			$transaction = $this->entityManager
				->getRepository(Transaction::class)
				->findOneBy(['transactionExtNumber' => $uuid]);
			
			if (is_null($transaction)) {
				return $uuid;
			}
		}
		
		return \App\Utils\UUID::v5();
	}
	
	/**
	 * Collect customer information
	 *
	 * @param User $customer
	 * @return array
	 */
	public function getCustomerInfo(User $customer): array
	{
		$customerInformation = [
			'cust_id' => $customer->getId(),
			'cust_email' => $customer->getEmail(),
		];
		
		if (!empty($customer->getFirstName())) {
			$customerInformation['cust_first_name'] = $customer->getFirstName();
		}
		
		if (!empty($customer->getLastName())) {
			$customerInformation['cust_last_name'] = $customer->getFirstName();
		}
		
		if (!empty($customer->getBillingAddress()->getAddress())) {
			$customerInformation['cust_address'] = $customer->getBillingAddress()->getAddress();
		}
		
		if (!empty($customer->getBillingAddress()->getPhone())) {
			$customerInformation['cust_phone'] = $customer->getBillingAddress()->getPhone();
		}
		
		if (!empty($customer->getBillingAddress()->getZipCode())) {
			$customerInformation['cust_zip'] = $customer->getBillingAddress()->getZipCode();
		}
		
		if (!empty($customer->getBillingAddress()->getCity())) {
			$customerInformation['cust_city'] = $customer->getBillingAddress()->getCity();
		}
		
		if (!empty($customer->getBillingAddress()->getCountry())) {
			$customerInformation['cust_country'] = $customer->getBillingAddress()->getCountry();
		}
		
		if (!empty($customer->getLanguage())) {
			$customerInformation['language'] = $customer->getLanguage();
		}
		
		return $customerInformation;
	}
	
	/**
	 * Get transaction status
	 *
	 * @param string|null $status
	 * @param string|null $locale  Forcing the Translator Locale
	 *
	 * @return string
	 */
	public function getTransactionStatus(?string $status, ?string $locale): string
	{
		if ($status === "ABANDONED") {
			return $this->translator->trans('status.abandonne', array(), 'PaymentServiceProvider', $locale);
		} elseif ($status === "AUTHORISED") {
			return  $this->translator->trans('status.accepted', array(), 'PaymentServiceProvider', $locale);
		} elseif ($status === "AUTHORISED_TO_VALIDATE") {
			return  $this->translator->trans('status.waiting_manually_validated', array(), 'PaymentServiceProvider', $locale);
		} elseif ($status === "WAITING_AUTHORISATION") {
			return  $this->translator->trans('status.waiting_for_authorisation', array(), 'PaymentServiceProvider', $locale);
		} elseif ($status === "EXPIRED") {
			return  $this->translator->trans('status.expired', array(), 'PaymentServiceProvider', $locale);
		} elseif ($status === "CANCELLED") {
			return  $this->translator->trans('status.cancelled', array(), 'PaymentServiceProvider', $locale);
		} elseif ($status === "WAITING_AUTHORISATION_TO_VALIDATE") {
			return $this->translator->trans('status.waiting_for_authorisation_and_manually_validated', array(), 'PaymentServiceProvider', $locale);
		} elseif ($status === "CAPTURED") {
			return $this->translator->trans('status.cashed', array(), 'PaymentServiceProvider', $locale);
		} elseif ($status === "NOT_CREATED") {
			return $this->translator->trans('status.cashed', array(), 'PaymentServiceProvider', $locale);
		} elseif ($status === "INITIAL") {
			return $this->translator->trans('status.cashed', array(), 'PaymentServiceProvider', $locale);
		} elseif ($status === "CAPTURE_FAILED") {
			return $this->translator->trans('status.cashed', array(), 'PaymentServiceProvider', $locale);
		} elseif ($status === "UNDER_VERIFICATION") {
			return $this->translator->trans('status.cashed', array(), 'PaymentServiceProvider', $locale);
		}
		
		return  $this->translator->trans('status.refused', array(), 'PaymentServiceProvider', $locale);
	}
	
	/**
	 * Get transaction warranty
	 *
	 * @param string|null $warranty
	 * @param string|null $locale  Forcing the Translator Locale
	 *
	 * @return string
	 */
	public function getTransactionWarranty(?string $warranty, ?string $locale): string
	{
		if ($warranty === "YES") {
			return  $this->translator->trans('warranty.guaranteed', array(), 'PaymentServiceProvider', $locale);
		} elseif ($warranty ===  "NO") {
			return  $this->translator->trans('warranty.not_guaranteed', array(), 'PaymentServiceProvider', $locale);
		} elseif ($warranty === "UNKNOWN") {
			return  $this->translator->trans('warranty.technical_error', array(), 'PaymentServiceProvider', $locale);
		} else {
			return $this->translator->trans('warranty.not_applicable', array(), 'PaymentServiceProvider', $locale);
		}
	}
	
	/**
	 * Get transaction delay
	 *
	 * @param string|null $delay
	 * @param string|null $locale  Forcing the Translator Locale
	 *
	 * @return string
	 */
	public function getTransactionDelay(?string $delay, ?string $locale): string
	{
		return sprintf("%s %02d %s", $this->translator->trans('delay.title', array(), 'PaymentServiceProvider', $locale), $delay, $this->translator->trans('delay.days', array(), 'PaymentServiceProvider', $locale), array(), 'PaymentServiceProvider', $locale);
	}
	
	/**
	 * Return invalid signature message
	 *
	 * @param null|string $locale
	 * @return string
	 */
	public function getInvalidSignatureMessage(?string $locale): string
	{
		return $this->translator->trans('invalid_signature', array(), 'PaymentServiceProvider', $locale);
	}
}