<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

use App\Validator\Validators\PromoTypeValidator;
use Symfony\Component\Validator\Exception\MissingOptionsException;

use App\Entity\User;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class PromoType extends Constraint
{
    /**
     * @var string error code
     */
    public const WRONG_ACCOUNT_ERROR = 'b91e69be-cde1-47ff-a076-9cc6bbbc625f';
    public const WRONG_ORGANIZATION_ERROR = '2dc1d183-c8cf-4f78-a0d8-bee756a5e5fc';

    /**
     * @var array
     */
    protected static $errorNames = array(
      self::WRONG_ACCOUNT_ERROR => 'WRONG_ACCOUNT_ERROR',
      self::WRONG_ORGANIZATION_ERROR => 'WRONG_ORGANIZATION_ERROR'
    );

    /**
     * @var string message text
     */
    public $wrongAccountErrorMessage = 'promo.msg.wrong_account_error';
    public $wrongAgencyErrorMessage = 'promo.msg.wrong_agency_error';
    public $wrongNetworkErrorMessage = 'promo.msg.wrong_network_error';

    /**
     * @var User
     */
    protected $user;

    /**
     * Constructor
     */
    public function __construct(?array $options = null)
    {
        if (null !== $options && !\is_array($options)) {
            $options = array(
            'user' => null
        );
        }
        parent::__construct($options);
    }

    /**
     * Get the value of User
     *
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy()
    {
        return PromoTypeValidator::class ;
    }
}
