<?php

namespace PN\Bundle\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="PN\Bundle\CMSBundle\Repository\PostRepository")
 */
class Post
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var array
     *
     * @ORM\Column(name="content", type="json_array")
     */
    protected $content = [
        'brief' => '',
        'description' => '',
    ];

    /**
     * @ORM\ManyToMany(targetEntity="\PN\Bundle\MediaBundle\Entity\Image", inversedBy="posts")
     */
    protected $images;

    /**
     * @ORM\OneToOne(targetEntity="DynamicPage", mappedBy="post")
     */
    protected $dynamicPage;


    public function getRelationalEntity() {
        $excludeMethods = ['__construct', '__clone', 'getMainImage', 'getImageByType', 'addImage', 'removeImage', 'getImages', 'getId', 'setContent', 'getContent', 'removeDocument', 'getDocuments', 'addDocument', 'getRelationalEntity'];

        $allMethods = get_class_methods($this);
        foreach ($allMethods as $method) {
            if (in_array($method, $excludeMethods) OR strpos($method, 'get') === FALSE) {
                continue;
            }
            if ($this->$method() != NULL) {
                return $this->$method()->getId();
            }
        }
        return NULL;
    }

    /**
     * Get Main Image
     *
     * @return \PN\Bundle\MediaBundle\Entity\Image
     */
    public function getMainImage() {
        return $this->getImages(array(\PN\Bundle\MediaBundle\Entity\Image::TYPE_MAIN))->first();
    }

    /**
     * Get Image By Type
     *
     * @return \PN\Bundle\MediaBundle\Entity\Image
     */
    public function getImageByType($type) {
        return $this->getImages(array($type))->first();
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages($types = null) {
        if ($types) {
            return $this->images->filter(function(\PN\Bundle\MediaBundle\Entity\Image $image) use ($types) {
                return in_array($image->getImageType(), $types);
            });
        } else {
            return $this->images;
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
     * Set content
     *
     * @param array $content
     *
     * @return Post
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add image
     *
     * @param \PN\Bundle\MediaBundle\Entity\Image $image
     *
     * @return Post
     */
    public function addImage(\PN\Bundle\MediaBundle\Entity\Image $image) {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \PN\Bundle\MediaBundle\Entity\Image $image
     */
    public function removeImage(\PN\Bundle\MediaBundle\Entity\Image $image) {
        $this->images->removeElement($image);
    }

    /**
     * Set dynamicPage
     *
     * @param \PN\Bundle\CMSBundle\Entity\DynamicPage $dynamicPage
     *
     * @return Post
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
