<?php
namespace SC\FOSRestExtensionBundle\Processor;

use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use FOS\UserBundle\Model\UserManagerInterface;
use SC\FOSRestExtensionBundle\Model\BaseUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * This is the processor that will handle all metadata that we get from
 * users when they connect
 */
class APIConnectionProcessor
{
    protected $user_manager;
    
    protected $token;

    public function __construct(UserManagerInterface $user_manager, TokenStorageInterface $token)
    {
        $this->user_manager = $user_manager;
        $this->token = $token;
    }

    public function updateLastLogin(PostResponseEvent $event)
    {
        $user = $this->token->getToken()->getUser();
        if ($user instanceof BaseUserInterface) {
            $user->setLastLogin(new \DateTime());
            $this->user_manager->updateUser($user);
        }
    }

    public function countConnections(PostResponseEvent $event)
    {
        $user = $this->token->getToken()->getUser();
        if ($user instanceof BaseUserInterface) {
            $user_metadata = $user->getMetadata();
            $user_metadata->setApiRequests($user_metadata->getApiRequests() + 1);
            $this->user_manager->updateUser($user);
        }
    }
}