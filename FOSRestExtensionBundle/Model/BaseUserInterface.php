<?php
namespace SC\FOSRestExtensionBundle\Model;

interface BaseUserInterface
{
    /**
     * Set metadata
     *
     * @param UserMetadataInterface $metadata
     *
     * @return BaseUserInterface
     */
    public function setMetadata(UserMetadataInterface $metadata = null);
    
    /**
     * Get metadata
     *
     * @return UserMetadataInterface
     */
    public function getMetadata(): UserMetadataInterface;
}