<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

use App\Validator\Validators\PromoExpirationValidator;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class PromoExpiration extends Constraint
{
    /**
     * @var string error code
     */
    public const EXPIRE_ERROR = '0adebe33-3cd3-43f3-916c-c08f3de16ba9';

    /**
     * @var array
     */
    protected static $errorNames = array(
      self::EXPIRE_ERROR => 'EXPIRE_ERROR',
    );

    /**
     * @var string message text
     */
    public $message = 'promo.msg.expired_error';

    /**
     * @inheritDoc
     */
    public function validatedBy()
    {
        return PromoExpirationValidator::class ;
    }
}
