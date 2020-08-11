<?php

namespace App\Events;

use Symfony\Component\EventDispatcher\Event;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Transaction;

class OrderEvent extends Event
{
    const TYPE_VALIDATION_ERROR = 'validation_error';
    const TYPE_ORDER_ERROR = 'order_error';
    const TYPE_FILE_SYSTEM_ERROR = 'type_file_system_error';
    const TYPE_DATABASE_ERROR = 'type_database_error';

    /**
     * @var string
     */
    protected $errorType;
    protected $errorMsg;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @var string
     */
    protected $uploadFolder;

    /**
     * @var string
     */
    protected $orderStatus;

    /**
     * Constructor
     *
     * @param array|null $data
     * @param User $user
     * @param Order $order
     * @param Transaction $transaction
     * @param null|string $orderStatus
     */
    public function __construct(?array $data, ?User $user, ?Order $order = null, ?Transaction $transaction = null, ?string $orderStatus = Order::AWAITING_FOR_PAYMENT)
    {
        $this->data = $data;
        $this->user = $user;
        $this->order = $order;

        $this->transaction = $transaction;
        $this->orderStatus = $orderStatus;

        $this->errorType = static::TYPE_ORDER_ERROR;
    }

    /**
     * Get the cuurent data
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Get the cuurent user
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Get the cuurent OrderCreation
     *
     * @param Order $order
     * @return void
     */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    /**
     * Get the cuurent OrderCreation
     *
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * Get the cuurent upload Directory
     *
     * @param string $uploadFolder
     * @return void
     */
    public function setUploadFolder(string $uploadFolder): void
    {
        $this->uploadFolder = $uploadFolder;
    }

    /**
     * Get the cuurent transaction
     *
     * @return Transaction
     */
    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    /**
     * Get the order status
     *
     * @return string
     */
    public function getOrderStatus(): ?string
    {
        return $this->orderStatus;
    }

    /**
     * Get the cuurent upload Directory
     *
     * @return string|null
     */
    public function getUploadFolder(): ?string
    {
        return $this->uploadFolder;
    }

    /**
     * Set the value of Error Msg
     *
     * @param mixed $errorMsg
     * @return self
     */
    public function setErrorMsg($errorMsg): self
    {
        $this->errorMsg = $errorMsg;

        return $this;
    }

    /**
     * Get the value of Error Msg
     *
     * @return mixed
     */
    public function getErrorMsg()
    {
        return $this->errorMsg;
    }

    /**
     * @return string
     */
    public function getErrorType(): string
    {
        return $this->errorType;
    }

    /**
     * @param string $errorType
     * @return OrderEvent
     */
    public function setErrorType(string $errorType): self
    {
        $this->errorType = $errorType;
        return $this;
    }
}
