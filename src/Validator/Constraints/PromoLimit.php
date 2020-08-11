<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

use App\Validator\Validators\PromoLimitValidator;

use App\Entity\User;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class PromoLimit extends Constraint
{
    /**
     * @var string error code
     */
    public const MAX_PICTURE_USE_ERROR = '50c6a63a-4027-4f72-bd81-9d16edc5adfc';
    public const MAX_USE_ERROR = '8b1fc6d8-b8bf-416b-b311-51445899c921';
    public const MAX_USE_PER_USER_ERROR = '8b1fc6d8-b8bf-416b-b311-51445899c921';

    /**
     * @var array
     */
    protected static $errorNames = array(
      self::MAX_PICTURE_USE_ERROR => 'MAX_PICTURE_USE_ERROR',
      self::MAX_USE_ERROR => 'MAX_USER_USE_ERROR',
      self::MAX_USE_PER_USER_ERROR => 'MAX_USER_USE_PER_USER_ERROR'
    );

    /**
     * @var string message text
     */
    public $maxPictureUseMessage = 'promo.msg.max_picture_use_error';
    public $maxUseMessage = 'promo.msg.max_use_error';
    public $maxUsePerUserMessage = 'promo.msg.max_use_per_user_error';

    /**
     * @var User
     */
    protected $user;
	
	/**
	 * Constructor
	 *
	 * @param array|null $options
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
        return PromoLimitValidator::class ;
    }
}
