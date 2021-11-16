<?php

namespace PN\Bundle\MediaBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("image")
 * @ORM\Entity(repositoryClass="PN\Bundle\MediaBundle\Repository\ImageRepository")
 */
class Image
{

    const TYPE_TEMP = 0;
    const TYPE_MAIN = 1;
    const TYPE_GALLERY = 2;
    const TYPE_ICON = 3;


    private $filenameForRemove;
    private $filenameForRemoveResize;
    private $directoryForRemove;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(name="base_path", type="string", length=255, nullable=true)
     */
    protected $basePath;
    

    /**
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     */
    protected $alt;

    /**
     * @ORM\Column(name="width", type="float", length=255, nullable=true)
     */
    protected $width;

    /**
     * @ORM\Column(name="height", type="float", length=255, nullable=true)
     */
    protected $height;

    /**
     * @ORM\Column(name="size", type="float", length=255, nullable=true)
     */
    protected $size;

    /**
     * @ORM\Column(name="cdn_server", type="smallint", nullable=true)
     */
    protected $cdnServer;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $imageType;


    /**
     * @ORM\ManyToMany(targetEntity="\PN\Bundle\CMSBundle\Entity\Post", mappedBy="images")
     */
    protected $posts;


    /**
     *
     * @Assert\NotBlank()
     */
    protected $file;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }


    public function getWebExtension($directory)
    {
        return null === $this->name ? null : $this->getUploadDir($directory) . '/' . $this->name;
    }

    protected function getUploadRootDir($directory)
    {
        // the absolute directory extension where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../../' . \AppKernel::$webRoot . $this->getUploadDir($directory);
    }

    public function getUploadDirForResize($directory)
    {
        // the absolute directory extension where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../../' . \AppKernel::$webRoot . $this->getUploadDir($directory);
    }


    public function getAssetPath()
    {
        return 'uploads/' . $this->getBasePath() . "/" . $this->getName();
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Image
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNameWithoutExtension()
    {
        return substr($this->name,0, strrpos($this->name, '.'));
    }

    public function getNameExtension()
    {
        return substr($this->name, strrpos($this->name, '.') + 1);
    }

    public function preUpload($generatedImageName = NULL)
    {
        if (null !== $this->file) {
            if ($generatedImageName != NULL) {
                $this->name = $generatedImageName . '-' . $this->id . '.' . $this->file->guessExtension();
            } else {
                $this->name = $this->id . '.' . $this->file->guessExtension();
            }
        }
    }

    public function upload($directory)
    {

        if (null === $this->file) {
            return;
        }
        $this->setBasePath($directory);
        // you must throw an exception here if the file cannot be moved
        // so that the entity is not persisted to the database
        // which the UploadedFile move() method does
        $this->file->move($this->getUploadRootDir($directory), $this->getName());
        unset($this->file);
    }

    public function storeFilenameForRemove($directory)
    {
        $this->filenameForRemove = $this->getAbsoluteExtension($directory);
    }

    public function removeUpload()
    {
        if ($this->getCdnServer() != NULL) {
            $conn = ftp_connect(\AppKernel::CDN_HOST);
            ftp_login($conn, \AppKernel::CDN_USERNAME, \AppKernel::CDN_PASSWORD);
        }

        if ($this->filenameForRemove) {
            if (file_exists($this->filenameForRemove)) {
                unlink($this->filenameForRemove);
                $folder = substr($this->filenameForRemove, 0, strrpos($this->filenameForRemove, '/') + 1);
                $folderContent = scandir($folder);
                if (count($folderContent) == 2) {
                    rmdir($folder);
                }
            } elseif ($this->getCdnServer() != NULL) {
                $file = "/uploads/" . $this->getBasePath() . "/" . $this->getName();
                ftp_delete($conn, $file);
            }
        }
        if ($this->filenameForRemoveResize) {
            if (file_exists($this->filenameForRemoveResize)) {
                unlink($this->filenameForRemoveResize);
                $folder = substr($this->filenameForRemoveResize, 0, strrpos($this->filenameForRemoveResize, '/') + 1);
                $folderContent = scandir($folder);
                if (count($folderContent) == 2) {
                    rmdir($folder);
                }
            } elseif ($this->getCdnServer() != NULL) {
                $file = "/uploads/" . $this->getBasePath() . "/thumb/" . $this->getName();
                ftp_delete($conn, $file);
            }
        }
        if (isset($conn)) {
            ftp_close($conn);
        }
    }

    public function storeDirectoryForRemove($directory)
    {
        $this->directoryForRemove = $this->getUploadDirForResize($directory);
    }

    public function removeDirectoryUpload()
    {
        if ($this->directoryForRemove) {
            rmdir($this->directoryForRemove);
        }
    }

    public function getAbsoluteExtension($directory)
    {
        return null === $this->name ? null : $this->getUploadRootDir($directory) . '/' . $this->name;
    }

    public function storeFilenameForResizeRemove($directory)
    {
        $this->filenameForRemoveResize = $this->getAbsoluteResizeExtension($directory);
    }

    public function removeResizeUpload()
    {
        if ($this->filenameForRemoveResize) {
            unlink($this->filenameForRemoveResize);
        }
    }

    public function getAbsoluteResizeExtension($directory)
    {
        $thumpPath = $this->getUploadRootDir($directory) . '/thumb/';
        if (!file_exists($thumpPath)) {
            mkdir($thumpPath, 0777, TRUE);
        }
        return null === $this->name ? null : $thumpPath . $this->name;
    }

    protected function getUploadDir($directory)
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/' . $directory;
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


    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set imageType
     *
     * @param integer $imageType
     * @return Image
     */
    public function setImageType($imageType)
    {
        $this->imageType = $imageType;

        return $this;
    }

    /**
     * Get imageType
     *
     * @return integer
     */
    public function getImageType()
    {
        return $this->imageType;
    }

    /**
     * Set basePath
     *
     * @param string $basePath
     * @return Image
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;

        return $this;
    }

    /**
     * Get basePath
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Set alt
     *
     * @param string $alt
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set width
     *
     * @param float $width
     * @return Image
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return float
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param float $height
     * @return Image
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return float
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set size
     *
     * @param float $size
     * @return Image
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return float
     */
    public function getSize()
    {
        return $this->size;
    }


    /**
     * Set cdnServer
     *
     * @param integer $cdnServer
     * @return Image
     */
    public function setCdnServer($cdnServer)
    {
        $this->cdnServer = $cdnServer;

        return $this;
    }

    /**
     * Get cdnServer
     *
     * @return integer
     */
    public function getCdnServer()
    {
        return $this->cdnServer;
    }

    /**
     * Add post
     *
     * @param \PN\Bundle\CMSBundle\Entity\Post $post
     *
     * @return Image
     */
    public function addPost(\PN\Bundle\CMSBundle\Entity\Post $post)
    {
        $this->posts[] = $post;

        return $this;
    }

    /**
     * Remove post
     *
     * @param \PN\Bundle\CMSBundle\Entity\Post $post
     */
    public function removePost(\PN\Bundle\CMSBundle\Entity\Post $post)
    {
        $this->posts->removeElement($post);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    public function getFirstPost()
    {
        return $this->posts->first();
    }

}
