<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Unique extends Constraint
{
    /**
     * @var string
     */
    public $message = 'The value {value} is already in use.';

    /**
     * @var string
     */
    public $entityClass;

    /**
     * @var string|array
     */
    public $field;

    /**
     * @return array
     */
    public function getRequiredOptions(): array
    {
        return ['field', 'entityClass'];
    }
}