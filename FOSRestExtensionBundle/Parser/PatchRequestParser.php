<?php
namespace SC\FOSRestExtensionBundle\Parser;

use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use SC\FOSRestExtensionBundle\Processor\PatchRequestProcessor;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\ContextFactory\DefaultSerializationContextFactory;

class PatchRequestParser
{
    protected $request_parameters;
    
    protected $em;
    
    protected $request;
    
    protected $router;
    
    protected $processor;
    
    protected $serializer;
    
    public function parse(string $resourceRepository,Request $request) 
    {
        $this->request = $request;
        $this->request_parameters = $this->router->match($this->request->getPathInfo());
        
        $entity = $this->getEntity($resourceRepository);
        $this->processor->read($this->request, true);
        $this->processor->applyChanges($entity);
        $c = new SerializationContext();
        $c->enableMaxDepthChecks();
        $arr = json_decode($this->serializer->serialize($entity, "json", $c), true);
        
        $p_resources = $this->processor->getPathResources();
        
        foreach ($arr as $key => $peace) {
            if (in_array($key, $p_resources) || in_array(lcfirst(ResourceParser::camelize($key)), $p_resources) || in_array(ResourceParser::underscore($key), $p_resources)) {
                if ("array" == gettype($peace)) {
                    $peace = $this->getKeyValueFromArray($peace);
                }
                $request->request->set($key, $peace);
            }
        }
    }

    
    protected function getKeyValueFromArray(array $arr)
    {
        if (key_exists("id", $arr)) {
            return $arr["id"];
        } elseif (array_keys($arr) === range(0, count($arr) -1)) {
            $ids = [];
            foreach ($arr as $peace) {
                if (key_exists("id", $peace)) {
                    array_push($ids, $peace["id"]);
                }
            }
            return $ids;
        }
    }

    public function __construct(
        RouterInterface $router, 
        EntityManagerInterface $em, 
        PatchRequestProcessor $processor,
        SerializerInterface $serializer
    )
    {
        $this->router = $router;
        $this->em = $em;
        $this->processor = $processor;
        $this->serializer = $serializer;
    }
    
    protected function getEntity(string $resourceRepository)
    {
        $entity = $this->em->getRepository($resourceRepository)->find($this->request_parameters["id"]);
        if (null === $entity) {
            throw new InvalidResourceException("Could not find specified resource to patch");
        }
        $this->em->detach($entity);
        return $entity;
    }
}