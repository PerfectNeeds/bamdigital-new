<?php

namespace PN\Bundle\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PN\Bundle\ServiceBundle\Entity\DateTimeTrait;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * SeoPage
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="seo_page")
 * @ORM\Entity(repositoryClass="PN\Bundle\SeoBundle\Repository\SeoPageRepository")
 */
class SeoPage
{

    use DateTimeTrait;

    const SEO_HOME=1;
    const SEO_CONTACT_US=2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="\PN\Bundle\SeoBundle\Entity\Seo", inversedBy="seoPage", cascade={"persist", "remove" })
     */
    protected $seo;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=100)
     */
    private $title;

    /**
     * Now we tell doctrine that before we persist or update we call the updatedTimestamps() function.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
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
     * Set seo
     *
     * @param \PN\Bundle\SeoBundle\Entity\Seo $seo
     * @return SeoPage
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
     * Set title
     *
     * @param string $title
     *
     * @return SeoPage
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
}

