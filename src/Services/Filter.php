<?php

namespace App\Services;

class Filter
{
    private array $filterMappings = [
        'recent' => 'createdAt',
        'likes' => 'numberOfLike',
        'views' => 'numberOfView',
    ];

    public function getMappedField(string $filter): ?string
    {
        return $this->filterMappings[$filter] ?? null;
    }

    public function isAllowedFilter(string $sort): bool
    {
        $allowedSorts = ['recent', 'likes', 'views'];
        return in_array($sort, $allowedSorts);
    }
}
