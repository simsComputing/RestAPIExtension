<?php
namespace SC\FOSRestExtensionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use SC\FOSRestExtensionBundle\Model\UserMetadataInterface;
use SC\FOSRestExtensionBundle\Model\BaseUserInterface;

class UserMetadata implements UserMetadataInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="integer", nullable=false, options={ "default": 0 })
     */
    protected $apiRequests = 0;
    
    
    /**
     * Set apiRequests
     *
     * @param integer $apiRequests
     *
     * @return UserMetadata
     */
    public function setApiRequests(int $apiRequests): UserMetadataInterface
    {
        $this->apiRequests = $apiRequests;
        
        return $this;
    }
    
    /**
     * Get apiRequests
     *
     * @return integer
     */
    public function getApiRequests(): int
    {
        return $this->apiRequests;
    }
    
    /**
     * Set user
     *
     * @param BaseUserInterface $user
     *
     * @return UserMetadataInterface
     */
    public function setUser(BaseUserInterface $user): UserMetadataInterface
    {
        $this->user = $user;
        
        return $this;
    }
    
    /**
     * Get user
     *
     * @return BaseUserInterface
     */
    public function getUser(): BaseUserInterface
    {
        return $this->user;
    }
}