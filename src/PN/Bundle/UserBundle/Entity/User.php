<?php

namespace PN\Bundle\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use PN\Bundle\ServiceBundle\Entity\DateTimeTrait;
use PN\Bundle\ServiceBundle\Entity\VirtualDeleteTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="usr")
 * @ORM\Entity(repositoryClass="PN\Bundle\UserBundle\Repository\UserRepository")
 */
class User extends BaseUser {

    const ROLE_ADMIN = "ROLE_ADMIN";
    const ROLE_SEO = "ROLE_SEO";
    const ROLE_IMAGE_GALLERY = "ROLE_IMAGE_GALLERY";


    use DateTimeTrait,
        VirtualDeleteTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     * @ORM\Column(name="fullname", type="string", length=255)
     */
    protected $fullName;


    /**
     * Now we tell doctrine that before we persist or update we call the updatedTimestamps() function.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps() {
        $this->setModified(new \DateTime(date('Y-m-d H:i:s')));

        if ($this->getCreated() == null) {
            $this->setCreated(new \DateTime(date('Y-m-d H:i:s')));
        }
    }

    public function __construct() {
        parent::__construct();


        // your own logic
    }

    public function setEmail($email)
    {
        parent::setEmail($email);
        $this->setUsername($email);

        return $this;
    }

    /**
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     * @return User
     */
    public function setFullName($fullName) {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName() {
        return $this->fullName;
    }

}
