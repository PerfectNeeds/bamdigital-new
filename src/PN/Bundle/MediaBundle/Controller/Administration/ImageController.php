<?php

namespace PN\Bundle\MediaBundle\Controller\Administration;

use PN\Utils\Slug;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Image controller.
 *
 * @Route("/image")
 */
class ImageController extends Controller
{


    /**
     * @Route("/edit", name="image_edit")
     * @Method("POST")
     */
    public function editAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $data=$request->request->get('data');
        $names=$data['name'];
        $alts=$data['alt'];

        foreach ($alts as $imageId=>$alt){
            $image=$em->getRepository('MediaBundle:Image')->find($imageId);
            if($image){
                $alt = Slug::sanitize($alt);
                $image->setAlt($alt);
                $em->persist($image);
            }
        }
        foreach ($names as $imageId=>$imageName){
            $image=$em->getRepository('MediaBundle:Image')->find($imageId);
            if($image){
                $extension = $image->getNameExtension();

                $oldPath = $image->getAbsoluteExtension($image->getBasePath());
                $oldThumbPath = $image->getAbsoluteResizeExtension($image->getBasePath());
                $imageName = Slug::sanitize($imageName);

                $image->setName($imageName . '.' . $extension);
                $em->persist($image);

                $newPath = $image->getAbsoluteExtension($image->getBasePath());
                $newThumbPath = $image->getAbsoluteResizeExtension($image->getBasePath());


                $checkName=$em->getRepository('MediaBundle:Image')->checkImageNameExistNotId($image->getName(),$image->getId());
                if ($checkName) {
                    $this->addFlash('error','Duplicate image name');
                    return $this->redirect($request->headers->get('referer'));
                }
                if(file_exists($oldPath)){
                    rename($oldPath,$newPath);
                }

                if (file_exists($oldThumbPath)) {
                    rename($oldThumbPath,$newThumbPath);
                }

            }
        }
        $em->flush();
        $this->addFlash('success','Images updated successfully');
        return $this->redirect($request->headers->get('referer'));
    }

}
