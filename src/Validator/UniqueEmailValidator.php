<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Repository\EmployeeRepository;

class UniqueEmailValidator extends ConstraintValidator
{
    public function __construct(private EmployeeRepository $employeeRepository)
    {
    }

    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            // If email is empty, let other constraints (NotBlank, etc.) handle it
            return;
        }

        // Look for an existing Employee with this same email
        $existing = $this->employeeRepository->findOneBy(['email' => $value]);
        if ($existing !== null) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
