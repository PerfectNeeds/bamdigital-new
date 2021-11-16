<?php

namespace PN\Bundle\MediaBundle\Service;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Routing\Router;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use PN\Bundle\MediaBundle\Entity\Image,
    PN\Bundle\MediaBundle\Utils\SimpleImage,
    PN\Bundle\MediaBundle\Entity\ImageSetting;
use PN\Utils\Slug;

class UploadImageService
{

    private $allowMimeType = array('image/gif', 'image/jpeg', 'image/jpg', 'image/png');
    private $type = array(
        100 => 'banner/',
    );
    protected $entityManager;
    protected $context;
    protected $router;
    protected $container;
    public $assets;

    public function __construct($entityManager, Router $router, Container $container)
    {
        $this->em = $entityManager;
        $this->router = $router;
        $this->container = $container;

    }

    public function uploadSingleImageByPath($entity, $path, $type, $request = NULL, $imageType = Image::TYPE_MAIN)
    {
        $file = new File($path);
        return $this->uploadSingleImage($entity, $file, $type, $request, $imageType);
    }

    public function uploadSingleImage($entity, $file, $type, Request $request = NULL, $imageType = Image::TYPE_MAIN)
    {

        $image = '';
        if ($file != null) {
            $mimeType = $file->getMimeType();
            if (!in_array($mimeType, $this->allowMimeType)) {
                $message = "Filetype not allowed";
                if ($request != NULL) {
                    $request->getSession()->getFlashBag()->add('error', $message);
                    return FALSE;
                } else {
                    return $message;
                }
            }
            if (getimagesize($file->getRealPath()) == FALSE) {
                $message = "invalid Image type";
                if ($request != NULL) {
                    $request->getSession()->getFlashBag()->add('error', $message);
                    return FALSE;
                } else {
                    return $message;
                }
            }

            $generatedImageName = NULL;
            if (!array_key_exists($type, $this->type)) {
                $imageSetting = $this->em->getRepository('MediaBundle:ImageSetting')->find($type);
                $uploadPath = $imageSetting->getUploadPath();
                $mainEntityId = $entity->getRelationalEntity(); // Product, Category, etc
                $generatedImageName = $this->getRowName($imageSetting->getEntityName(), $mainEntityId);
            } else {
                $uploadPath = $this->type[$type];
            }

            if (method_exists($entity->getId(), 'getId')) {
                $imageId = $entity->getId()->getId();
            } else {
                $imageId = $entity->getId();
            }

            if ($imageType == Image::TYPE_MAIN) {
                if (method_exists($entity, 'getImageByType')) {
                    $oldImage = $entity->getImageByType($imageType);
                } else {
                    $oldImage = $entity->getImage();
                }

                if ($oldImage) {
                    $oldImage->storeFilenameForRemove($uploadPath . 'image/' . $imageId);
                    $oldImage->storeFilenameForResizeRemove($uploadPath . 'image/' . $imageId);
                    $oldImage->removeUpload();
                    if (method_exists($entity, 'removeImage')) {
                        $entity->removeImage($oldImage);
                    } else {
                        $entity->setImage(NULL);
                    }
                    $this->em->remove($oldImage);
                    $this->em->persist($entity);
                    $this->em->flush();
                }
            }

            $image = new Image();
            $this->em->persist($image);
            $this->em->flush();
            $image->setFile($file);
            $image->setImageType($imageType);
            $image->preUpload($generatedImageName);
            $fullImagePath = $uploadPath . 'image/' . $imageId;

            $image->upload($fullImagePath);

            if (!array_key_exists($type, $this->type)) {
                if ($imageSetting->getAutoResize() == TRUE) {
                    list($width, $height) = getimagesize($image->getUploadDirForResize($fullImagePath) . "/" . $image->getName());
                    $oPath = $image->getUploadDirForResize($fullImagePath) . "/" . $image->getName();

                    if ($imageSetting->getQuality() == ImageSetting::ORIGINAL_RESOLUTION) {
                        $quality = 100;
                    } else {
                        $quality = 75;
                    }
                    SimpleImage::saveNewResizedImage($oPath, $oPath, $width, $height, $quality);

                    if ($imageType == Image::TYPE_MAIN) {
                        if ($imageSetting->getTypeId($imageType) != NULL) {
                            $widthDefault = $imageSetting->getTypeId($imageType)->getWidth();
                            $heightDefault = $imageSetting->getTypeId($imageType)->getHeight();

                            if (($widthDefault and $width > $widthDefault) || ($heightDefault and $height > $heightDefault)) {
                                SimpleImage::saveNewResizedImage($oPath, $oPath, $widthDefault, $heightDefault, $quality);
                            }
                            $thumbWidthDefault = $imageSetting->getTypeId($imageType)->getThumbWidth();
                            $thumbHeightDefault = $imageSetting->getTypeId($imageType)->getThumbHeight();
                            if ($thumbWidthDefault != NULL || $thumbHeightDefault != NULL) {
                                $resize_2 = $image->getAbsoluteResizeExtension($fullImagePath);
                                SimpleImage::saveNewResizedImage($oPath, $resize_2, $thumbWidthDefault, $thumbHeightDefault);
                            }
                        }
                    }

                }

                $size = filesize($image->getUploadDirForResize($fullImagePath) . "/" . $image->getName());
                list($width, $height) = getimagesize($image->getUploadDirForResize($fullImagePath) . "/" . $image->getName());
                $image->setWidth($width);
                $image->setHeight($height);
                $image->setSize($size);
            }

            if (method_exists($entity, 'addImage')) {
                $entity->addImage($image);
            } else {
                $entity->setImage($image);
            }

            $this->em->persist($entity);
            $this->em->flush();
        }
        return $image;
    }

    public function deleteImage($entity, $image, $type)
    {
        if (!array_key_exists($type, $this->type)) {
            $imageSetting = $this->em->getRepository('MediaBundle:ImageSetting')->find($type);
            $uploadPath = $imageSetting->getUploadPath();
        } else {
            $uploadPath = $this->type[$type];
        }


        if (method_exists($entity, 'removeImage')) {
            $entity->removeImage($image);
        } else {
            $entity->setImage(NULL);
        }
        $this->em->persist($entity);
        $this->em->flush();


        $image->storeFilenameForRemove($uploadPath . $entity->getId());
        $image->removeUpload();
        $this->em->persist($image);
        $this->em->flush();
        $this->em->remove($image);
        $this->em->flush();
    }

    public function getRowName($entityName, $id = NULL)
    {
        if ($id == NULL) {
            return NULL;
        }
        $em = $this->em;
        $entities = $em->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();
        $className = NULL;
        foreach ($entities as $entity) {
            $loopEntityName = substr($entity, strrpos($entity, '\\') + 1);
            if (strpos($entity, 'PN\Bundle') === FALSE OR $loopEntityName != $entityName) {
                continue;
            }
            $path = explode('\Entity\\', $entity);
            $className = str_replace('\\', '', str_replace('PN\Bundle', '', $path[0])) . ':' . $path[1];
        }
        if ($entityName == NULL) {
            return NULL;
        }
        $entity = $em->find($className, $id);
        if (!$entity) {
            return NULL;
        }

        if (method_exists($entity, "getTitle")) {
            return Slug::sanitize($entity->getTitle());
        } elseif (method_exists($entity, "getName")) {
            return Slug::sanitize($entity->getName());
        }
        return NULL;
    }


}