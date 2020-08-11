<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

use App\Validator\Validators\PromoNotNullValidator;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class PromoNotNull extends Constraint
{
    /**
     * @var string error code
     */
    const NOT_VALID_PROMO_ERROR = '20e1ba3f-f75d-490d-bd91-c1040c684b8d';

    /**
     * @var array
     */
    protected static $errorNames = array(
        self::NOT_VALID_PROMO_ERROR => 'NOT_VALID_PROMO_ERROR',
    );

    /**
     * @var string message text
     */
    public $message = 'promo.msg.not_valid_promo_error';

    /**
     * @inheritDoc
     */
    public function validatedBy()
    {
        return PromoNotNullValidator::class ;
    }
}
