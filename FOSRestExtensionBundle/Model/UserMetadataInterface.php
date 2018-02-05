<?php
namespace SC\FOSRestExtensionBundle\Model;

interface UserMetadataInterface
{
    /**
     * Set apiRequests
     *
     * @param integer $apiRequests
     *
     * @return UserMetadataInterface
     */
    public function setApiRequests(int $apiRequests);
    
    /**
     * Get apiRequests
     *
     * @return integer
     */
    public function getApiRequests(): int;
    
    /**
     * Set user
     *
     * @param BaseUserInterface $user
     *
     * @return UserMetadataInterface
     */
    public function setUser(BaseUserInterface $user);
    
    /**
     * Get user
     *
     * @return BaseUserInterface
     */
    public function getUser(): BaseUserInterface;
}