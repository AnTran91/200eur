<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * A wrapper for holding data to be used for a application/problem+json response
 */
class ApiProblem
{
    /**
     * @var string
     */
    const TYPE_FILE_SYSTEM_ERROR = 'type_file_system_error';
    const TYPE_DATABASE_ERROR = 'type_database_error';
    const TYPE_VALIDATION_ERROR = 'validation_error';
    const TYPE_ORDER_ERROR = 'order_error';
    const TYPE_INVALID_REQUEST_BODY_FORMAT = 'invalid_body_format';
    const TYPE_ORDER_NOT_READY_ERROR = 'order_not_ready';

    /**
     * @var array
     */
    private static $titles = array(
        self::TYPE_FILE_SYSTEM_ERROR => 'There was a file system  error',
        self::TYPE_DATABASE_ERROR => 'There was a database error',
        self::TYPE_VALIDATION_ERROR => 'There was a validation error',
        self::TYPE_ORDER_NOT_READY_ERROR => 'The requested order is not ready',
        self::TYPE_ORDER_ERROR => 'There was a order creation error',
        self::TYPE_INVALID_REQUEST_BODY_FORMAT => 'Invalid JSON format sent',
    );

    /**
     * @var string
     */
    private $statusCode;

    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed|string
     */
    private $title;

    /**
     * @var array
     */
    private $extraData = array();

    /**
     * ApiProblem constructor.
     *
     * @param $statusCode
     * @param null $type
     */
    public function __construct($statusCode, $type = null)
    {
        $this->statusCode = $statusCode;

        if ($type === null) {
            $title = isset(Response::$statusTexts[$statusCode])
                ? Response::$statusTexts[$statusCode]
                : 'Unknown status code :()';
        } else {
            if (!isset(self::$titles[$type])) {
                throw new \InvalidArgumentException('No title for type '.$type);
            }

            $title = self::$titles[$type];
        }

        $this->type = $type;
        $this->title = $title;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(
            array(
                'code' => $this->statusCode,
                'title' => $this->title,
            ),
            $this->extraData
        );
    }

    /**
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $this->extraData[$name] = $value;
    }

    /**
     * @param $value
     */
    public function add($value)
    {
        $this->extraData[] = $value;
    }

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return mixed|string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
