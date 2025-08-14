<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

class PaginatedEmployeesResponse
{
    const DEFAULT_GROUP = 'Default';
    #[Groups([self::DEFAULT_GROUP])]
    public int $page;

    #[Groups([self::DEFAULT_GROUP])]
    public int $limit;

    #[Groups([self::DEFAULT_GROUP])]
    public int $totalItems;

    #[Groups([self::DEFAULT_GROUP])]
    public int $totalPages;

    /** @var array<int, mixed> */
    #[Groups([self::DEFAULT_GROUP])]
    public array $items = [];

    public function __construct(int $page, int $limit, int $totalItems, array $items)
    {
        $this->page = $page;
        $this->limit = $limit;
        $this->totalItems = $totalItems;
        $this->totalPages = (int) ceil($totalItems / $limit);
        $this->items = $items;
    }
}
