<?php

namespace Illarra\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @ORM\Entity(repositoryClass="Illarra\CoreBundle\Repository\User")
 * @ORM\Table(name="admin_user")
 */
class User implements UserInterface, \Serializable
{
    use \Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
    
    public function __toString()
    {
        return $this->username;
    }
    
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=25, unique=true)
     */
    protected $username;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32)
     * @Serializer\Exclude
     */
    protected $salt;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=40)
     */
    protected $password;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=60, unique=true)
     */
    protected $email;
    
    /**
     * @var array
     *
     * @ORM\Column(type="array", nullable=true)
     */
    protected $roles = [];
    
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $isActive;
    
    public function __construct()
    {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        $this->addRole('ROLE_USER');
    }
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * @param string $username
     * @return this
     */
    public function setUsername($username)
    {
        $this->username = $username;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }
    
    /**
     * @param string $password
     * @return this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * @param string $email
     * @return this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * @inheritDoc
     */
    public function getIsActive()
    {
        return $this->password;
    }
    
    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }
    
    /**
     * @param string $role
     * @return this
     */
    public function addRole($role)
    {
        $this->roles[] = $role;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }
    
    /**
     * @inheritDoc
     */
    public function equals(UserInterface $user)
    {
        return $this->id === $user->getId();
    }
    
    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }
    
    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
        ) = unserialize($serialized);
    }
}
