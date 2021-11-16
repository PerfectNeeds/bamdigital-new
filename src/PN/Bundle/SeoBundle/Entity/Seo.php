<?php

namespace PN\Bundle\SeoBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping as ORM;

/**
 * Seo
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("seo", uniqueConstraints={@UniqueConstraint(name="slug_unique", columns={"slug", "seo_base_route_id"})})
 * @ORM\Entity(repositoryClass="PN\Bundle\SeoBundle\Repository\SeoRepository")
 */
class Seo
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="SeoBaseRoute", inversedBy="seos")
     */
    protected $seoBaseRoute;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="slug", type="string", length=255)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="text" , nullable=true)
     */
    protected $metaDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="focus_keyword", type="string" , nullable=true)
     */
    protected $focusKeyword;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_keyword", type="string" , nullable=true)
     */
    protected $metaKeyword;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="smallint", nullable=true)
     */
    protected $state;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $lastModified;

    /**
     * @ORM\Column(name="deleted", type="boolean", options={"default" = 0}),nullable=false)
     */
    protected $deleted = false;

    /**
     * @ORM\OneToMany(targetEntity="SeoSocial", mappedBy="seo", cascade={"persist", "remove" })
     */
    protected $seoSocials;

    /**
     * @ORM\OneToOne(targetEntity="SeoPage", mappedBy="seo")
     */
    protected $seoPage;

    /**
     * @ORM\OneToOne(targetEntity="\PN\Bundle\CMSBundle\Entity\DynamicPage", mappedBy="seo")
     */
    protected $dynamicPage;



    public function __clone()
    {
        $this->id = NULL;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->seoSocials = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Now we tell doctrine that before we persist or update we call the updatedTimestamps() function.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setLastModified(new \DateTime(date('Y-m-d H:i:s')));
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Seo
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Seo
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
     * Set metaDescription
     *
     * @param string $metaDescription
     * @return Seo
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Seo
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set focusKeyword
     *
     * @param string $focusKeyword
     *
     * @return Seo
     */
    public function setFocusKeyword($focusKeyword)
    {
        $this->focusKeyword = $focusKeyword;

        return $this;
    }

    /**
     * Get focusKeyword
     *
     * @return string
     */
    public function getFocusKeyword()
    {
        return $this->focusKeyword;
    }

    /**
     * Set metaKeyword
     *
     * @param string $metaKeyword
     *
     * @return Seo
     */
    public function setMetaKeyword($metaKeyword)
    {
        $this->metaKeyword = $metaKeyword;

        return $this;
    }

    /**
     * Get metaKeyword
     *
     * @return string
     */
    public function getMetaKeyword()
    {
        return $this->metaKeyword;
    }

    /**
     * Add seoSocial
     *
     * @param \PN\Bundle\SeoBundle\Entity\SeoSocial $seoSocial
     *
     * @return Seo
     */
    public function addSeoSocial(\PN\Bundle\SeoBundle\Entity\SeoSocial $seoSocial)
    {
        $this->seoSocials[] = $seoSocial;

        return $this;
    }

    /**
     * Remove seoSocial
     *
     * @param \PN\Bundle\SeoBundle\Entity\SeoSocial $seoSocial
     */
    public function removeSeoSocial(\PN\Bundle\SeoBundle\Entity\SeoSocial $seoSocial)
    {
        $this->seoSocials->removeElement($seoSocial);
    }

    /**
     * Get seoSocials
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSeoSocials($types = null)
    {
        if ($types) {
            return $this->seoSocials->filter(function (SeoSocial $seoSocial) use ($types) {
                return in_array($seoSocial->getSocialNetwork(), $types);
            });
        } else {
            return $this->seoSocials;
        }
    }

    /**
     * Get seoSocial By Type
     *
     * @return \PN\Bundle\SeoBundle\Entity\SeoSocial
     */
    public function getSeoSocialByType($type)
    {
        return $this->getSeoSocials(array($type))->first();
    }

    /**
     * Set seoBaseRoute
     *
     * @param \PN\Bundle\SeoBundle\Entity\SeoBaseRoute $seoBaseRoute
     *
     * @return Seo
     */
    public function setSeoBaseRoute(\PN\Bundle\SeoBundle\Entity\SeoBaseRoute $seoBaseRoute = null)
    {
        $this->seoBaseRoute = $seoBaseRoute;

        return $this;
    }

    /**
     * Get seoBaseRoute
     *
     * @return \PN\Bundle\SeoBundle\Entity\SeoBaseRoute
     */
    public function getSeoBaseRoute()
    {
        return $this->seoBaseRoute;
    }

    /**
     * Set seo
     *
     * @param \PN\Bundle\SeoBundle\Entity\SeoPage $seoPage
     *
     * @return Seo
     */
    public function setSeoPage(\PN\Bundle\SeoBundle\Entity\SeoPage $seoPage = null)
    {
        $this->seoPage = $seoPage;

        return $this;
    }

    /**
     * Get seoPage
     *
     * @return \PN\Bundle\SeoBundle\Entity\SeoPage
     */
    public function getSeoPage()
    {
        return $this->seoPage;
    }


    /**
     * Set lastModified
     *
     * @param \DateTime $lastModified
     * @return Seo
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * Get lastModified
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return Seo
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }



    /**
     * Set dynamicPage
     *
     * @param \PN\Bundle\CMSBundle\Entity\DynamicPage $dynamicPage
     *
     * @return Seo
     */
    public function setDynamicPage(\PN\Bundle\CMSBundle\Entity\DynamicPage $dynamicPage = null)
    {
        $this->dynamicPage = $dynamicPage;

        return $this;
    }

    /**
     * Get dynamicPage
     *
     * @return \PN\Bundle\CMSBundle\Entity\DynamicPage
     */
    public function getDynamicPage()
    {
        return $this->dynamicPage;
    }


}
