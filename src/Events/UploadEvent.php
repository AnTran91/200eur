<?php

namespace App\Events;

use Symfony\Component\EventDispatcher\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Form;

use App\Entity\Order;

class UploadEvent extends Event
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var string
     */
    protected $targetFolder;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var int
     */
    protected $code;
	
	/**
	 * Constructor
	 *
	 * @param   Request     $request        The current request
	 * @param   string      $targetFolder   Custom File upload directory
	 * @param   Form        $form           File upload form
	 * @param   Order|null  $order          The current Order
	 */
    public function __construct(Request $request, string $targetFolder, ?Form $form = null, ?Order $order = null)
    {
        $this->request = $request;
        $this->targetFolder = $targetFolder;
        $this->form = $form;
        $this->order = $order;
    }
	
	/**
	 * Sets the data to be sent as JSON.
	 *
	 * @param mixed $data
	 *
	 * @return void
	 */
    public function setData(array $data = array()): void
    {
        $this->data = $data;
    }

    /**
     * Sets the response status code.
     *
     * @param bool	$success
     *
     * @return void
     */
    public function setStatusCode(bool $success): void
    {
        $this->code = Response::HTTP_OK;

        if (!$success) {
            $this->code = Response::HTTP_BAD_REQUEST;
        }
    }

    /**
     * Get the cuurent request
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Gets the data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Gets the response status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->code;
    }

    /**
     * Get Form.
     *
     * @return Form
     */
    public function getForm(): Form
    {
        return $this->form;
    }

    /**
     * Get file upload target directory
     *
     * @return string
     */
    public function getTargetFolder(): string
    {
        return $this->targetFolder;
    }

    /**
     * get the value of OrderCreation
     *
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }
}
