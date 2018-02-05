<?php
namespace SC\FOSRestExtensionBundle\QueryFilter;


interface QueryFilterInterface
{
    public function setFilters(string $className);
    public function getExactFilters(): array;
    public function getMatchFilters(): array;
}