<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

use App\Validator\Validators\PromoDateIntervalValidator;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class PromoDateInterval extends Constraint
{
    /**
     * @var string error code
     */
    public const DATE_INTERVAL_ERROR = '414de559-5101-4566-a434-bddf42608a99';

    /**
     * @var array
     */
    protected static $errorNames = array(
        self::DATE_INTERVAL_ERROR => 'DATE_INTERVAL_ERROR',
    );

    /**
     * @var string message text
     */
    public $message = 'promo.msg.date_interval_error';

    /**
     * @inheritDoc
     */
    public function validatedBy()
    {
        return PromoDateIntervalValidator::class ;
    }
}
