<?php

namespace App\Events;

use Symfony\Component\EventDispatcher\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Form;

use App\Entity\PictureDetails;
use App\Entity\Picture;
use App\Entity\Order;
use App\Entity\User;

class ChunkedFileUploadEvent extends Event
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var PictureDetails
     */
    protected $pictureDetail;

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
     * @param   Request           $request          The cuurent request
     * @param   PictureDetails    $pictureDetail    Picture details
     * @param   Form              $form             File upload form
     */
    public function __construct(Request $request, ?PictureDetails $pictureDetail = null, ?Form $form = null)
    {
        $this->request = $request;
        $this->pictureDetail = $pictureDetail;
        $this->form = $form;
    }

    /**
     * Sets the data to be sent as JSON.
     *
     * @param mixed	$data
     *
     * @return void
     */
    public function setData($data = array()): void
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
    public function setSuccess(bool $success): void
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
    public function getData()
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
     * Get Picture details
     *
     * @return PictureDetails
     */
    public function getPictureDetail(): PictureDetails
    {
        return $this->pictureDetail;
    }

    /**
     * Get Picture
     *
     * @return Picture
     */
    public function getPicture(): Picture
    {
        return $this->pictureDetail->getPicture();
    }

    /**
     * Get OrderCreation
     *
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->pictureDetail->getPicture()->getOrder();
    }

    /**
     * Get returned Picture
     *
     * @return null|Picture
     */
    public function getReturnedPicture(): ?Picture
    {
        return $this->pictureDetail->getReturnedPicture();
    }

    /**
     * Get client
     *
     * @return null|Picture
     */
    public function getClient(): ?User
    {
        return $this->pictureDetail->getPicture()->getOrder()->getClient();
    }
}
