<?php

namespace PN\Bundle\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PN\Bundle\ServiceBundle\Entity\DateTimeTrait;

/**
 * DynamicPage
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="dynamic_page")
 * @ORM\Entity(repositoryClass="PN\Bundle\CMSBundle\Repository\DynamicPageRepository")
 */
class DynamicPage
{
    use DateTimeTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="\PN\Bundle\SeoBundle\Entity\Seo", inversedBy="dynamicPage", cascade={"persist", "remove" })
     */
    protected $seo;

    /**
     * @ORM\OneToOne(targetEntity="Post", inversedBy="dynamicPage", cascade={"persist", "remove" })
     */
    protected $post;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=50)
     */
    protected $title;


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

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return DynamicPage
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set seo
     *
     * @param \PN\Bundle\SeoBundle\Entity\Seo $seo
     *
     * @return DynamicPage
     */
    public function setSeo(\PN\Bundle\SeoBundle\Entity\Seo $seo = null)
    {
        $this->seo = $seo;

        return $this;
    }

    /**
     * Get seo
     *
     * @return \PN\Bundle\SeoBundle\Entity\Seo
     */
    public function getSeo()
    {
        return $this->seo;
    }

    /**
     * Set post
     *
     * @param \PN\Bundle\CMSBundle\Entity\Post $post
     *
     * @return DynamicPage
     */
    public function setPost(\PN\Bundle\CMSBundle\Entity\Post $post = null)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \PN\Bundle\CMSBundle\Entity\Post
     */
    public function getPost()
    {
        return $this->post;
    }
}
