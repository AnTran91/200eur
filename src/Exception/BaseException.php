<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Exception;

/**
 * Exception is the base exception class.
 */
class BaseException extends \RuntimeException
{
    /**
     * constructor
     *
     * @param string  $message
     */
    public function __construct($message)
    {
	    if (is_array($message)){
		    $pieces = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($message));
		
		    $message = "";
		    foreach ($pieces as $piece){
			    $message .= (string) $piece;
		    }
	    }
	    
        parent::__construct($message, 0);
    }
}
