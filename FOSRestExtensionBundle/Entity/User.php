<?php
namespace SC\FOSRestExtensionBundle\Entity;

use SC\FOSRestExtensionBundle\Model\BaseUserInterface;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SC\FOSRestExtensionBundle\Model\UserMetadataInterface;

/**
 * @author simscomputing
 */
class User extends BaseUser implements BaseUserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToOne(targetEntity="UserMetadata", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $metadata;
    
    /**
     * Set metadata
     *
     * @param UserMetadata $metadata
     *
     * @return BaseUserInterface
     */
    public function setMetadata(UserMetadataInterface $metadata = null): BaseUserInterface
    {
        $this->metadata = $metadata;
        $metadata->setUser($this);
        
        return $this;
    }
    
    /**
     * Get metadata
     *
     * @return UserMetadataInterface
     */
    public function getMetadata(): UserMetadataInterface
    {
        return $this->metadata;
    }
}