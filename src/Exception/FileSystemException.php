<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Exception;

/**
 * File system Exception
 */
class FileSystemException extends BaseException
{
    /**
     * Constructor
     *
     * @param mixed $message
     */
    public function __construct($message = '')
    {
        parent::__construct($message);
    }
}
