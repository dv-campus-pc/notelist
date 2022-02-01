<?php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

class PaginationService
{
    public function paginator(QueryBuilder $queryBuilder, Request $request, ?int $limit = 10): Paginator
    {
        $queryBuilder
            ->setMaxResults($limit)
            ->setFirstResult($limit * ($this->getCurrentPage($request) - 1));

        return new Paginator($queryBuilder);
    }

    public function lastPage(Paginator $paginator): int
    {
        return 10;
    }

    private function getCurrentPage(Request $request): int
    {
        return max($request->query->getInt('page', 1), 1);
    }
}