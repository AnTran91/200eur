<?php

/**
 * (c) Sfari Rami <rami2sfari@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\PaymentServiceProvider;

/**
 * The SystemPayProviderInterface handle payment by systempay form
 */
interface SystemPayProviderInterface
{
	/**
	 * Set System pay required fields
	 *
	 * @param array $options
	 * @return void
	 */
    public function prepareTransaction(array $options = array()): void;

    /**
     * Set prefix to all fields
     *
     * @return array
     */
    public function getRequest(): array;
	
	/**
	 * Check signature and return the result
	 *
	 * @param array   $query
	 * @param string  $locale
	 *
	 * @return array
	 */
	public function responseHandler(array $query, string $locale): array;
}
