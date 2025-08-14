<?php

namespace App\Validator;

use App\Repository\CompanyRepository;
use App\Repository\ProjectRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueProjectNameValidator extends ConstraintValidator
{
    public function __construct(private ProjectRepository $repository) {}

    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            // If email is empty, let other constraints (NotBlank, etc.) handle it
            return;
        }

        // Look for an existing Employee with this same email
        $existing = $this->repository->findOneBy(['name' => $value]);
        if ($existing !== null) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
