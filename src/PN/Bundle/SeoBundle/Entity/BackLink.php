<?php

namespace PN\Bundle\SeoBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * BackLink
 * @ORM\Table("back_link")
 * @ORM\Entity(repositoryClass="PN\Bundle\SeoBundle\Repository\BackLinkRepository")
 */
class BackLink {

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * 
     * @Assert\NotBlank()
     * @ORM\Column(name="word", type="string", length=255, nullable=false, unique = true)
     */
    protected $word;

    /**
     * @var string
     * 
     * @Assert\NotBlank()
     * @Assert\Url()
     * @ORM\Column(name="link", type="text", nullable=false)
     */
    protected $link;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set word
     *
     * @param string $word
     * @return BackLink
     */
    public function setWord($word) {
        $this->word = $word;

        return $this;
    }

    /**
     * Get word
     *
     * @return string 
     */
    public function getWord() {
        return $this->word;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return BackLink
     */
    public function setLink($link) {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink() {
        return $this->link;
    }

}
