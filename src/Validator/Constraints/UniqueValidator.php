<?php
namespace App\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UniqueValidator
 *
 * @package App\Validator
 */
class UniqueValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * DeleteEntity constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint|Unique $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value === null) {
            return;
        }

        $entity = $this->manager->getRepository($constraint->entityClass)->findBy([$constraint->field => $value], null, 1);
        if ($entity) {
            $this->context->addViolation($constraint->message, ['{value}' => $value]);
        }
    }
}