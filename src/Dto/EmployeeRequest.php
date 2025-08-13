<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\UniqueEmail;

class EmployeeRequest
{
    public const CREATE = 'create';
    public const UPDATE = 'update';
    const EMPLOYEE_WRITE = 'employee:write';

    #[Assert\NotBlank(groups: [self::CREATE])]
    #[Assert\Regex(
        pattern: '/^[\p{L}\s-]+$/u',
        message: 'This value should contain only letters, spaces, or dashes.'
    )]
    #[Assert\Length(
        min: 2,
        max: 30,
        minMessage: 'The name must be at least {{ limit }} characters long.',
        maxMessage: 'The name cannot be longer than {{ limit }} characters.'
    )]
    #[Groups([self::EMPLOYEE_WRITE])]
    private ?string $firstName = null;

    #[Assert\NotBlank(groups: [self::CREATE])]
    #[Assert\Regex(
        pattern: '/^[\p{L}\s-]+$/u',
        message: 'This value should contain only letters, spaces, or dashes.'
    )]
    #[Assert\Length(
        min: 2,
        max: 30,
        minMessage: 'The name must be at least {{ limit }} characters long.',
        maxMessage: 'The name cannot be longer than {{ limit }} characters.'
    )]
    #[Groups([self::EMPLOYEE_WRITE])]
    private ?string $lastName = null;

    #[Assert\NotBlank(groups: [self::CREATE])]
    #[Assert\Email(message: 'Please provide a valid email address.')]
    #[Assert\Length(max: 180)]
    #[UniqueEmail(groups: [self::CREATE, Constraint::DEFAULT_GROUP])]
    #[Groups([self::EMPLOYEE_WRITE])]
    private ?string $email = null;

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }
}
