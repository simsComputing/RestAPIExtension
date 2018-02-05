<?php
namespace SC\FOSRestExtensionBundle\Processor;

use Negotiation\Exception\InvalidArgument;
use SC\FOSRestExtensionBundle\PathDecoder\PathDecoder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpFoundation\Request;
use SC\FOSRestExtensionBundle\Parser\ResourceParser;

/**
 * @author simscomputing
 */
class PatchRequestProcessor
{
    const ACTION_UPDATE = "update";
    
    const ACTION_DELETE = "delete";
    
    const ACTION_DEFAULT = "default";
    
    const ACTION_ADD = "add";
    
    const ACTION_REMOVE = "remove";
    
    protected $ops = array();
    
    protected $paths = array();
    
    protected $values = array();
    
    protected $path_decoder;
    
    protected $body;
    
    protected $bans;
    
    public function __construct(PathDecoder $p_decoder, ContainerInterface $container)
    {
        $this->path_decoder = $p_decoder;
        $this->bans = $container->getParameter("scfos_rest_extension.patch_processor.banning");
    }
    
    protected function setBody(Request $request)
    {
        $this->body = $request->request->all();
    }
    
    public function read(Request $request, $delete = false)
    {
        $this->setBody($request);
        
        foreach ($this->body as $index => $peace) {
            if (! isset($peace["op"]) || ! isset($peace["path"]) || ! isset($peace["value"])) {
                throw new InvalidArgument("Missing keys in data provided for PATCH Action please refer to the documentation");
            }
            
            $regex = sprintf("/%s|%s|%s|%s|%s/", static::ACTION_UPDATE, static::ACTION_REMOVE, static::ACTION_DELETE, static::ACTION_DEFAULT, static::ACTION_ADD);
            
            if (! preg_match($regex, $peace["op"])) {
                throw new \UnexpectedValueException("Invalid value provided for op field");
            }
            
            array_push($this->ops, $peace["op"]);
            array_push($this->paths, $peace["path"]);
            if (ResourceParser::checkIsDate($peace["value"])) {
                $peace["value"] = new \DateTime($peace["value"]);
            }
            array_push($this->values, $peace["value"]);
            
            if ($delete) {
                $request->request->remove($index);
            }
        }
    }
    
    public function applyChanges($entity)
    {
        if (count($this->ops) != count($this->paths) || count($this->paths) != count($this->values)) {
            throw new \LogicException("Not as many paths as ops and/or values");
        }
        
        
        for ($i = 0; $i < count($this->ops); $i ++) {
            $method = $this->getOperation($this->ops[$i], $this->paths[$i]);
            if ($this->controlMethod(get_class($entity), $method)) {
                $entity->$method($this->values[$i]);
            } else {
                throw new UnauthorizedHttpException("","Impossible de modifier la resource ainsi.");
            }
        }
    }
    
    protected function getOperation(string $op, string $path)
    {
        switch ($op) {
            case static::ACTION_UPDATE:
            case static::ACTION_DELETE:
            case static::ACTION_DEFAULT:
                $operation = $this->getMethod($path);
                break;
            case static::ACTION_ADD:
                $operation = $this->addMethod($path);
                break;
            case static::ACTION_REMOVE:
                $operation = $this->removeMethod($path);
                break;
            default:
                $operation = null;
        }
        return $operation;
    }
    
    protected function getMethod(string $path)
    {
        $this->path_decoder->setPath($path);
        if ($this->path_decoder->isSubResource($path)) {
            return null;
        }
        $resource = $this->path_decoder->getResource(0);
        
        return $this->path_decoder->parse2Method("set", $resource);
    }
    
    protected function addMethod(string $path)
    {
        $this->path_decoder->setPath($path);
        if ($this->path_decoder->isSubResource($path)) {
            return null;
        }
        
        $resource = $this->path_decoder->getResource(0);
        
        return $this->path_decoder->parse2Method("add", $resource);
    }
    
    protected function removeMethod(string $path)
    {
        $this->path_decoder->setPath($path);
        if ($this->path_decoder->isSubResource($path)) {
            return null;
        }
        
        $resource = $this->path_decoder->getResource(0);
        
        return $this->path_decoder->parse2Method("remove", $resource);
    }
    
    protected function controlMethod(string $className, $method): bool {
        $method_camelized = ResourceParser::camelize($method);
        $method_underscored = ResourceParser::underscore($method);
        return (method_exists($className, $method_camelized) || method_exists($className, $method_underscored)) && !in_array($this->path_decoder->parse2Resource($method), $this->bans[$className]);
    }
    
    public function getPathResources() {
        $p_resources = [];
        foreach ($this->paths as $p) {
            $this->path_decoder->setPath($p);
            array_push($p_resources, ResourceParser::camelize($this->path_decoder->getResource(0)));
        }
        return $p_resources;
    }
}