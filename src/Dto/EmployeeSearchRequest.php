<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class EmployeeSearchRequest
{
    #[Assert\GreaterThanOrEqual(1)]
    private int $page = 1;

    #[Assert\GreaterThanOrEqual(1)]
    private int $limit = 10;

    private ?string $email = null;

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): self
    {
        $this->page = $page;
        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
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
