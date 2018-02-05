<?php
namespace SC\FOSRestExtensionBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use SC\FOSRestExtensionBundle\Model\APIControllerInterface;
use Symfony\Component\Intl\Exception\NotImplementedException;
use SC\FOSRestExtensionBundle\Parser\PatchRequestParser;

/**
 * @author simscomputing
 * @todo Add in the control method the config boolean of weither it's activated or not.
 */

class PatchRequestListener
{

    protected $parser;

    public function __construct(PatchRequestParser $parser)
    {
        $this->parser = $parser;
    }

    public function parseRequest(FilterControllerEvent $event)
    {
        if ($this->isPatchRequestParserEnabled($event)) {
            $controller = $event->getController()[0];
            if (! ($controller instanceof APIControllerInterface)) {
                throw new NotImplementedException(
                    sprintf("Controller %s should implement interface %s", get_class($controller), APIControllerInterface::class
                        ));
            }
            $resourceRepository = $controller->getResourceRepository();
            $this->parser->parse($resourceRepository, $event->getRequest());
        }
    }
    
    protected function isPatchRequestParserEnabled(FilterControllerEvent $event) {
        return $event->getRequest()->isMethod("PATCH");
    }
}