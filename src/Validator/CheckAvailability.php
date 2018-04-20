<?php
/**
 * Created by PhpStorm.
 * User: hello
 * Date: 20/04/2018
 * Time: 11:29.
 */

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/** @Annotation */
class CheckAvailability extends Constraint
{
    public function validatedBy()
    {
        return 'check_availability';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
