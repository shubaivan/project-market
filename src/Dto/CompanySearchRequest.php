<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CompanySearchRequest
{
    #[Assert\GreaterThanOrEqual(1)]
    private int $page = 1;

    #[Assert\GreaterThanOrEqual(1)]
    private int $limit = 10;

    private ?string $name = null;

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
