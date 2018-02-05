<?php
namespace SC\FOSRestExtensionBundle\PathDecoder;

use Symfony\Component\DependencyInjection\Container;

class PathDecoder
{
        
    protected $path;
    
    public function __construct() {
        
    }
    
    public function setPath(string $path) {
        if (strpos($path, "/") !== 0) {
            throw new \UnexpectedValueException("Invalid value provided for path field");
        }
        $this->path = $path;
    }
    
    public function getPath() {
        return $this->path;
    }
    
    public function isSubResource(string $path) {
        $r = explode("/", $path);
        if (count($r) > 2) {
            return true;
        }
        return false;
    }
    
    public function parse2Resource($method) {
        $underscored_exploded = explode("_", Container::underscore($method));
        $string_wo_action = array_slice($underscored_exploded, 1, count($underscored_exploded) - 1); 
        return lcfirst(Container::camelize(implode("_", $string_wo_action)));
    }
    
    public function parse2Method($action, $resource): string {
        if (strpos($resource, "_") !== false) {
            $resource = Container::camelize($resource);
        }
        return $action . ucfirst(strtolower($resource));
    }
    
    public function getResource(int $level) {
        if ($level < 0) {
            throw new \InvalidArgumentException(sprintf("Parameter 1 passed to %s::getResource should be an integer higher than 0. %s given", get_class($this), $level));
        }

        return explode("/", $this->path)[$level + 1];
    }
}