<?php

namespace App\Dto;

use App\Validator\UniqueCompanyName;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyRequest
{
    public const CREATE = 'create';
    public const UPDATE = 'update';
    const COMPANY_WRITE = 'company:write';

    #[Assert\NotBlank(groups: [self::CREATE])]
    #[Assert\Length(max: 180)]
    #[UniqueCompanyName(groups: [self::CREATE, Constraint::DEFAULT_GROUP])]
    #[Groups([self::COMPANY_WRITE])]
    private ?string $name = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
