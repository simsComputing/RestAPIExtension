<?php
namespace SC\FOSRestExtensionBundle\EventListener;

use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use SC\FOSRestExtensionBundle\Processor\APIConnectionProcessor;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Psr\Log\LoggerInterface;

/**
 *
 * @author simscomputing
 *        
 *         Listener that allows us to update lastlogindate of FOSUserBundle when we
 *         connect to the API using FOSOauthServerBundle.
 */
class APIConnectionListener
{

    protected $processor;

    protected $container;

    protected $logger;

    protected $event;

    public function __construct(APIConnectionProcessor $processor, ContainerInterface $container, LoggerInterface $logger)
    {
        $this->processor = $processor;
        $this->container = $container;
        $this->logger = $logger;
    }

    protected function enablementControl()
    {
        return $this->isMasterRequest() && $this->isOnPattern();
    }

    protected function isMasterRequest()
    {
        if ($this->event === null) {
            return false;
        }
        return $this->event->isMasterRequest();
    }

    protected function isOnPattern()
    {
        return preg_match(
            $this->container->getParameter("scfos_rest_extension.api_connection_processor.pattern"),
            $this->event->getRequest()->getPathInfo()
        );
    }

    public function enableProcessor(PostResponseEvent $event)
    {
        if (!$this->enablementControl()) {
            return;
        }
        $config = $this->container->getParameter("scfos_rest_extension.api_connection_processor.methods");
        foreach ($config as $method => $value) {
            if ($value) {
                $method = lcfirst(Container::camelize($method));
                if (method_exists($this->processor, $method)) {
                    $this->processor->$method($event);
                } else {
                    $this->logger->warning("APIConnectionListener was configured to process unexistant method : " . $method);
                }
            }
        }
    }
}