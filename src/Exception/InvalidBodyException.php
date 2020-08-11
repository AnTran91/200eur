<?php

namespace App\Exception;

/**
 * InvalidBodyException Raised when the request body is malformed.
 */
class InvalidBodyException extends BaseException
{
    /**
     * Constructor
     *
     * @param $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
        parent::setErrorCode(406);
    }
}