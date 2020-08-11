<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Exception;

use App\Exception\BaseException;

/**
 * File system Exception
 */
class PaymentException extends \RuntimeException
{
    /**
     * Constructor
     *
     * @param string  $message
     */
    public function __construct($message = '')
    {
        parent::__construct($message);
    }
}
