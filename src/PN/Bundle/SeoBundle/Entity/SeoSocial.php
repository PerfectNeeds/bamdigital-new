<?php

namespace PN\Bundle\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SeoSocial
 *
 * @ORM\Table(name="seo_social")
 * @ORM\Entity(repositoryClass="PN\Bundle\SeoBundle\Repository\SeoSocialRepository")
 */
class SeoSocial {

    const FACEBOOK = 1;
    const TWITTER = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Seo", inversedBy="seoSocials")
     */
    protected $seo;


    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text",nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="image_url", type="string", length=255,nullable=true)
     */
    private $imageUrl;

    /**
     * @var int
     *
     * @ORM\Column(name="social_network", type="smallint",nullable=true)
     */
    private $socialNetwork;

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
     * @return SeoSocial
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
     * Set description
     *
     * @param string $description
     *
     * @return SeoSocial
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set imageUrl
     *
     * @param string $imageUrl
     *
     * @return SeoSocial
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * Get imageUrl
     *
     * @return string
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * Set socialNetwork
     *
     * @param integer $socialNetwork
     *
     * @return SeoSocial
     */
    public function setSocialNetwork($socialNetwork)
    {
        $this->socialNetwork = $socialNetwork;

        return $this;
    }

    /**
     * Get socialNetwork
     *
     * @return int
     */
    public function getSocialNetwork()
    {
        return $this->socialNetwork;
    }

    /**
     * Set seo
     *
     * @param \PN\Bundle\SeoBundle\Entity\Seo $seo
     *
     * @return SeoSocial
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

}
