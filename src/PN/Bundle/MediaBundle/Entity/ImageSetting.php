<?php

namespace PN\Bundle\MediaBundle\Entity;

use PN\Bundle\ServiceBundle\Entity\DateTimeTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Table("image_setting")
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="PN\Bundle\MediaBundle\Repository\ImageSettingRepository")
 * @UniqueEntity("entityName",message="This entity name is used before.")
 */
class ImageSetting {

    use DateTimeTrait;


    const WEB_RESOLUTION = 1;
    const ORIGINAL_RESOLUTION = 2;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="entity_name", type="string", length=255, nullable=true, unique=true)
     */
    protected $entityName;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="back_route", type="string", length=255, nullable=true)
     */
    protected $backRoute;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="upload_path", type="string", length=255, nullable=true)
     */
    protected $uploadPath;

    /**
     * @ORM\Column(name="auto_resize", type="boolean")
     */
    protected $autoResize = true;

    /**
     * @ORM\Column(name="quality", type="smallint")
     */
    protected $quality;

    /**
     * @ORM\Column(name="gallery", type="boolean")
     */
    protected $gallery;

    /**
     * @ORM\OneToMany(targetEntity="ImageSettingHasType", mappedBy="imageSetting")
     */
    protected $imageSettingTypes;


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
     * Constructor
     */
    public function __construct() {
        $this->imageSettingTypes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set entityName
     *
     * @param string $entityName
     * @return ImageSetting
     */
    public function setEntityName($entityName) {
        $this->entityName = $entityName;

        return $this;
    }

    /**
     * Get entityName
     *
     * @return string 
     */
    public function getEntityName() {
        return $this->entityName;
    }

    /**
     * Set backRoute
     *
     * @param string $backRoute
     * @return ImageSetting
     */
    public function setBackRoute($backRoute) {
        $this->backRoute = $backRoute;

        return $this;
    }

    /**
     * Get backRoute
     *
     * @return string 
     */
    public function getBackRoute() {
        return $this->backRoute;
    }

    /**
     * Set uploadPath
     *
     * @param string $uploadPath
     * @return ImageSetting
     */
    public function setUploadPath($uploadPath) {
        $this->uploadPath = $uploadPath;

        return $this;
    }

    /**
     * Get uploadPath
     *
     * @return string 
     */
    public function getUploadPath() {
         return rtrim($this->uploadPath, '/') . '/';
    }

    /**
     * Set autoResize
     *
     * @param boolean $autoResize
     * @return ImageSetting
     */
    public function setAutoResize($autoResize) {
        $this->autoResize = $autoResize;

        return $this;
    }

    /**
     * Get autoResize
     *
     * @return boolean 
     */
    public function getAutoResize() {
        return $this->autoResize;
    }

    /**
     * Set quality
     *
     * @param integer $quality
     * @return ImageSetting
     */
    public function setQuality($quality) {
        $this->quality = $quality;

        return $this;
    }

    /**
     * Get quality
     *
     * @return integer 
     */
    public function getQuality() {
        return $this->quality;
    }

    /**
     * Set gallery
     *
     * @param boolean $gallery
     * @return ImageSetting
     */
    public function setGallery($gallery) {
        $this->gallery = $gallery;

        return $this;
    }

    /**
     * Get gallery
     *
     * @return boolean
     */
    public function getGallery() {
        return $this->gallery;
    }


    /**
     * Add imageSettingTypes
     *
     * @param \PN\Bundle\MediaBundle\Entity\ImageSettingHasType $imageSettingTypes
     * @return ImageSetting
     */
    public function addImageSettingType(\PN\Bundle\MediaBundle\Entity\ImageSettingHasType $imageSettingTypes) {
        $this->imageSettingTypes[] = $imageSettingTypes;

        return $this;
    }

    /**
     * Remove imageSettingTypes
     *
     * @param \PN\Bundle\MediaBundle\Entity\ImageSettingHasType $imageSettingTypes
     */
    public function removeImageSettingType(\PN\Bundle\MediaBundle\Entity\ImageSettingHasType $imageSettingTypes) {
        $this->imageSettingTypes->removeElement($imageSettingTypes);
    }

    /**
     * Get imageSettingTypes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImageSettingTypes() {
        return $this->imageSettingTypes;
    }

    public function getRadioButton() {
        $return = array();
        foreach ($this->imageSettingTypes as $imageSettingType) {
            if ($imageSettingType->getRadioButton() == TRUE) {
                array_push($return, $imageSettingType);
            }
        }
        return $return;
    }

    public function getNotRadioButton() {
        $return = array();
        foreach ($this->imageSettingTypes as $imageSettingType) {
            if ($imageSettingType->getRadioButton() == FALSE) {
                array_push($return, $imageSettingType);
            }
        }
        return $return;
    }

    public function getTypeId($typeId) {
        foreach ($this->imageSettingTypes as $imageSettingType) {
            if ($imageSettingType->getImageType()->getId() == $typeId) {
                return $imageSettingType;
            }
        }
        return FALSE;
    }

}
