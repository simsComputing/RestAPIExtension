<?php
namespace SC\FOSRestExtensionBundle\QueryFilter;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class QueryFilterGeneric implements QueryFilterInterface
{
    protected $stack;
    
    protected $request;
    
    protected $container;
    
    protected $exact_filter = array();
    
    protected $match_filter = array();
    
    public function __construct(RequestStack $stack, ContainerInterface $container) {
        $this->stack = $stack;
        $this->request = $stack->getCurrentRequest();
        $this->container = $container;
    }
    
    public function setFilters(string $className) {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Class provided for query filter doesn't exist : " . $className);
        }
        $param_name = "scfos_rest_extension.query_filters." . $className;
        
        if (!$this->container->hasParameter($param_name) || gettype($this->container->getParameter($param_name)) !== "array") {
            throw new InvalidConfigurationException("Missing fields parameters for " . $className . " entity queryfilters");    
        }
        $query = $this->request->query;
        foreach ($this->container->getParameter($param_name) as $filter) {
            if ($query->get("exact_" . $filter, null) !== null) {
                $this->exact_filter[$filter] = $query->get("exact_" . $filter);
            } elseif ($query->get("match_" . $filter, null) !== null) {
                $this->match_filter[$filter] = $query->get("match_" . $filter);
            }
        }
    }
    
    public function getExactFilters(): array
    {
        return $this->exact_filter;
    }
    
    public function getMatchFilters(): array
    {
        return $this->match_filter;
    }
}
