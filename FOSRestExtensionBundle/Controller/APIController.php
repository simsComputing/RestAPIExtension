<?php
namespace SC\FOSRestExtensionBundle\Controller;

use SC\FOSRestExtensionBundle\Model\APIControllerInterface;
use SC\FOSRestExtensionBundle\Exception\UndefinedConstantException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class APIController extends Controller implements APIControllerInterface
{
    public function getResourceRepository() {
        if (!defined("static::RESOURCE_REPOSITORY")) {
            throw new UndefinedConstantException(sprintf(
                "Controller %s doesn't have any RESOURCE constant defined. Please define a constant in order for the PatchProcessor to work.", get_class($this)));
        }
        return static::RESOURCE_REPOSITORY;
    }
}