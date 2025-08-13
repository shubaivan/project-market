<?php

namespace App\Dto;

class PaginatedEmployeesResponse
{
    public int $page;
    public int $limit;
    public int $totalItems;
    public int $totalPages;
    /** @var array<int, mixed> */
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
