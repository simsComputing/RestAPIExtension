<?php
namespace SC\FOSRestExtensionBundle\Model;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;

abstract class ResourceRepository extends EntityRepository
{
    
    const MAX_RESULTS_LIMIT = 100;
    
    protected function filterMaxResults(int $maxResult): int
    {
        if ($maxResult <= 0 || $maxResult === null || $maxResult > static::MAX_RESULTS_LIMIT) {
            return static::MAX_RESULTS_LIMIT;
        }
        return $maxResult;
    }
    
    protected function handle(QueryBuilder $builder, $maxResult = null): Query
    {
        if (null === $builder->getMaxResults()) {
            $builder->setMaxResults($this->filterMaxResults(intval($maxResult)));
        }
        return $builder->getQuery();
    }
    
    public function search(array $exact_criterias, array $match_criterias, int $offset = 0, int $limit = 20)
    {
        $queryBuilder = $this->createQueryBuilder("q");
        $match_str = array();
        foreach ($match_criterias as $c => $v) {
                array_push($match_str, "MATCH_AGAINST(q." . $c . ", :match_" . $c . " 'IN BOOLEAN MODE') > 0");
                $queryBuilder->setParameter("match_" . $c, $v);
        }
        $exact_str = array();
        foreach ($exact_criterias as $c => $v) {
                array_push($exact_str, "q." . $c . " = :exact_" . $c);
                $queryBuilder->setParameter("exact_" . $c, $v);
        }
        
        if (!empty($match_str)) {
            $match_str = count($match_str) > 1 ? implode(" OR ", $match_str) : $match_str[0];
            $queryBuilder->orWhere($match_str);
        }
        if (!empty($exact_str)) {
            $exact_str = count($exact_str) > 1 ? implode(" OR ", $exact_str) : $exact_str[0];
            $queryBuilder->orWhere($exact_str);
        }
        
        var_dump($queryBuilder->getQuery()->getDQL());
        
        if ($offset) {
            $queryBuilder->setFirstResult($offset);
        }
        
        return $this->handle($queryBuilder, $limit)->getResult();
    }
}