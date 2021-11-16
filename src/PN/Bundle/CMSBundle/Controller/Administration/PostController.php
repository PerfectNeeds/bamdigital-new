<?php

namespace PN\Bundle\CMSBundle\Controller\Administration;

use PN\Utils\General;
use PN\Utils\Slug;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use PN\Bundle\MediaBundle\Entity\Image;
use PN\Bundle\MediaBundle\Entity\ImageSetting;
use PN\Bundle\MediaBundle\Utils\SimpleImage;

/**
 * Post controller.
 *
 * @Route("post")
 */
class PostController extends Controller
{

    /**
     * @Route("/gallery/{id}/{pageType}/{parentId}", name="post_set_images")
     * @Method("GET")
     */
    public function imagesAction($id, $pageType, $parentId)
    {
        $this->denyAccessUnlessGranted('ROLE_IMAGE_GALLERY');

        $em = $this->getDoctrine()->getManager();
        $imageSetting = $em->getRepository('MediaBundle:ImageSetting')->find($pageType);
        if (!$imageSetting) {
            throw $this->createNotFoundException('Unable to find ImageSetting entity...');
        }
        $entity = $em->getRepository('CMSBundle:Post')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity...');
        }

        $entityName = $imageSetting->getEntityName();

        $meta = $em->getMetadataFactory()->getAllMetadata();
        $bundle = "";
        foreach ($meta as $m) {
            $name = (new \ReflectionClass($m->getName()))->getShortName();
            if ($name == $entityName) {
                $nameSpaces = explode('\\', $m->getName());
                if (count($nameSpaces) == 5) {
                    $bundle = $nameSpaces[2];
                }
            }
        }
        $returnEntity = $em->getRepository("$bundle:$entityName")->find($parentId);

        return $this->render('cms/admin/post/images.html.twig', [
            'entity' => $entity,
            'imageSetting' => $imageSetting,
            'parentId' => $parentId,
            'returnEntity' => $returnEntity,
        ]);
    }

    /**
     * Set Images to Property.
     *
     * @Route("/gallery/{id}/{pageType}" , name="post_create_images")
     * @Method("POST")
     */
    public function uploadImageAction(Request $request, $id, $pageType)
    {
        $this->denyAccessUnlessGranted('ROLE_IMAGE_GALLERY');

        $em = $this->getDoctrine()->getManager();

        $imageSetting = $em->getRepository('MediaBundle:ImageSetting')->find($pageType);
        if (!$imageSetting) {
            throw $this->createNotFoundException('Unable to find ImageSetting entity...');
        }

        $entity = $em->getRepository('CMSBundle:Post')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find post entity.');
        }
        $imageUploader = $this->get('upload_image');
        $files = $request->files->get('files');
        foreach ($files as $file) {
            $image = $imageUploader->uploadSingleImage($entity, $file, $pageType, $request, Image::TYPE_TEMP);
            $returnData [] = $this->renderView('cms/admin/post/imageItem.html.twig', [
                'image' => $image,
                'entity' => $entity,
                'imageSetting' => $imageSetting,
            ]);
        }
        return new JsonResponse($returnData);
    }

    /**
     * Deletes a PropertyGallery entity.
     *
     * @Route("/delete-image/{pageType}", name="post_images_delete")
     * @Method("POST")
     */
    public function deleteImageAction(Request $request, $pageType)
    {
        $this->denyAccessUnlessGranted('ROLE_IMAGE_GALLERY');

        $image_id = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $image = $em->getRepository('MediaBundle:Image')->find($image_id);
        if (!$image) {
            throw $this->createNotFoundException('Unable to find Team entity.');
        }
        $entity = $image->getFirstPost();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to Post Team entity.');
        }

        $imageSetting = $em->getRepository('MediaBundle:ImageSetting')->find($pageType);
        if (!$imageSetting) {
            throw $this->createNotFoundException('Unable to find ImageSetting entity...');
        }

        $entity->removeImage($image);
        $em->persist($entity);
        $em->flush();

        $image->storeFilenameForRemove($image->getBasePath());
        $image->storeFilenameForResizeRemove($image->getBasePath());

        $image->removeUpload();
        $em->remove($image);
        $em->flush();
        return new JsonResponse(['error' => 0, 'message' => 'Deleted successfully']);
    }


    /**
     * Deletes a MultiPropertyGallery entity.
     *
     * @Route("/delete-multi-image/{pageType}", name="post_images_multi_delete")
     * @Method("POST")
     */
    public function deleteMultiImageAction(Request $request, $pageType)
    {
        $this->denyAccessUnlessGranted('ROLE_IMAGE_GALLERY');

        $em = $this->getDoctrine()->getManager();

        $image_ids = $request->request->get('ids');
        $imageSetting = $em->getRepository('MediaBundle:ImageSetting')->find($pageType);
        if (!$imageSetting) {
            throw $this->createNotFoundException('Unable to find ImageSetting entity...');
        }

        if (count($image_ids) > 0) {
          foreach ($image_ids as $image_id){
              $image = $em->getRepository('MediaBundle:Image')->find($image_id);
              if (!$image) {
                  return new JsonResponse(['error' => 1, 'message' => 'Unable to find Image entity.']);

              }
              $post = $image->getFirstPost();
              if (!$post) {
                  return new JsonResponse(['error' => 1, 'message' => 'Unable to find Post entity.']);
              }

              $post->removeImage($image);
              $em->persist($post);
              $em->flush();

              $image->storeFilenameForRemove($image->getBasePath());
              $image->storeFilenameForResizeRemove($image->getBasePath());

              $image->removeUpload();
              $em->remove($image);
              $em->flush();
          }
        }

        return new JsonResponse(['error' => 0, 'message' => 'Deleted successfully']);
    }

    /**
     * Displays a form to create a new PropertyGallery entity.
     *
     * @Route("/gallery/type/ajax/{pageType}", name = "post_set_image_type_ajax")
     * @Method("POST")
     */
    public function setImageTypeAction(Request $request, $pageType, $imageType = Image::TYPE_MAIN)
    {
        $this->denyAccessUnlessGranted('ROLE_IMAGE_GALLERY');

        $type = $request->request->get('type');
        if (isset($type) AND $type != NULL) {
            $imageType = $type;
        }
        $em = $this->getDoctrine()->getManager();
        $imageSetting = $em->getRepository('MediaBundle:ImageSetting')->find($pageType);
        $image_id = $request->request->get('image_id');
        if ($imageSetting->getAutoResize() == TRUE) {
            $image = $em->getRepository('MediaBundle:Image')->find($image_id);

            $fullImagePath = $image->getBasePath();
            $oPath = $image->getUploadDirForResize($fullImagePath) . "/" . $image->getName();
            list($width, $height) = getimagesize($oPath);
            $widthDefault = $imageSetting->getTypeId($imageType)->getWidth();
            $heightDefault = $imageSetting->getTypeId($imageType)->getHeight();

            $quality = 100;

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
        $mainImage = $em->getRepository('MediaBundle:Image')->setMainImage('CMSBundle:Post', $image->getFirstPost()->getId(), $image_id, $imageType);

        $returnData [] = $this->renderView('cms/admin/post/imageItem.html.twig', [
            'image' => $mainImage,
            'entity' => $image->getFirstPost(),
            'imageSetting' => $imageSetting,
        ]);

        return new JsonResponse(['message' => 'Done', 'returnData' => $returnData]);
    }


    /**
     * update image name
     *
     * @Route("/update-image-name", name = "post_update_image_name_ajax")
     * @Method("POST")
     */
    public function updateImageNameAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_IMAGE_GALLERY');

        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');

        $image = $em->getRepository('MediaBundle:Image')->find($id);
        if (!$image) {
            return new JsonResponse(['error' => 1, 'message' => 'Image not found']);
        }
        $extension = $image->getNameExtension();

        $imageName = $request->request->get('imageName');

        if (!$imageName) {
            return new JsonResponse(['error' => 1, 'message' => 'Please enter image name']);
        }
        $oldImageName = $image->getNameWithoutExtension();
        $oldPath = $image->getAbsoluteExtension($image->getBasePath());
        $oldThumbPath = $image->getAbsoluteResizeExtension($image->getBasePath());
        $imageName = Slug::sanitize($imageName);
        $image->setName($imageName . '.' . $extension);

        $em->persist($image);

        $newPath = $image->getAbsoluteExtension($image->getBasePath());
        $newThumbPath = $image->getAbsoluteResizeExtension($image->getBasePath());

        $em->persist($image);

        $checkName = $em->getRepository('MediaBundle:Image')->checkImageNameExistNotId($image->getName(), $image->getId());
        if ($checkName) {
            return new JsonResponse(['error' => 1, 'message' => 'Duplicate image name', 'imageName' => $oldImageName]);
        }
        if (file_exists($oldPath)) {
            rename($oldPath, $newPath);
        }

        if (file_exists($oldThumbPath)) {
            rename($oldThumbPath, $newThumbPath);
        }

        $em->flush();

        return new JsonResponse(['error' => 0, 'message' => 'Image name updated successfully', 'imageName' => $image->getNameWithoutExtension()]);
    }


    /**
     * update image alt
     *
     * @Route("/update-image-alt", name = "post_update_image_alt_ajax")
     * @Method("POST")
     */
    public function updateImageAltAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_IMAGE_GALLERY');

        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');

        $image = $em->getRepository('MediaBundle:Image')->find($id);
        if (!$image) {
            return new JsonResponse(['error' => 1, 'message' => 'Image not found']);
        }
        $imageAlt = $request->request->get('imageAlt');
        $imageAlt = Slug::sanitize($imageAlt);

        $image->setAlt($imageAlt);
        $em->persist($image);
        $em->flush();

        return new JsonResponse(['error' => 0, 'message' => 'Image alt updated successfully', 'imageAlt' => $imageAlt]);
    }

}
