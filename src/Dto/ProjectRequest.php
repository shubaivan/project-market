<?php

namespace App\Dto;

use App\Validator\UniqueCompanyName;
use App\Validator\UniqueProjectName;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class ProjectRequest
{
    public const CREATE = 'create';
    public const UPDATE = 'update';
    const PROJECT_WRITE = 'project:write';

    #[Assert\NotBlank(groups: [self::CREATE])]
    #[Assert\Length(max: 180)]
    #[UniqueProjectName(groups: [self::CREATE, Constraint::DEFAULT_GROUP])]
    #[Groups([self::PROJECT_WRITE])]
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
