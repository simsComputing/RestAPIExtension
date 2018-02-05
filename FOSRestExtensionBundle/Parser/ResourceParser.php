<?php
namespace SC\FOSRestExtensionBundle\Parser;

class ResourceParser
{
    static function camelize($resource) 
    {
        return lcfirst(strtr(ucwords(strtr($resource, array('_' => ' ', '.' => '_ ', '\\' => '_ '))), array(' ' => '')));
    }
    
    static function underscore($id) 
    {
        return strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), array('\\1_\\2', '\\1_\\2'), $id));
    }
    
    static function checkIsDate($subject) {
        return preg_match("/^\d{4}-([1][0-2]|[0][1-9])-([0][1-9]|[1-2][0-9]|[3][0-1])T([2][0-3]|[0-1][0-9])(:[0-5][0-9]){2}[+|-]\d{2}:\d{2}$/", $subject);
    }
}