<?php
namespace App\Validator\Constraints;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class PasswordValidator
 * @package App\Validator
 */
class PasswordValidator extends ConstraintValidator
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * DeleteEntity constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->encoder = $encoder;
    }

    /**
     * @return mixed
     */
    private function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value === null) {
            return;
        }

        if (!$this->encoder->isPasswordValid($this->getUser(), $value)) {
            $this->context->buildViolation($constraint->{'message'})->addViolation();
        }
    }
}