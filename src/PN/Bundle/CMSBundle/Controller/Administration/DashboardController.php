<?php

namespace PN\Bundle\CMSBundle\Controller\Administration;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dashboard controller.
 *
 * @Route("/")
 */
class DashboardController extends Controller
{

    /**
     * Lists all Faq entities.
     *
     * @Route("/", name="dashboard")
     * @Method("GET")
     */
    public function DashboardAction()
    {

        return $this->render('cms/admin/dashboard/Dashboard.html.twig');
    }


    /**
     * Lists entity tabs.
     *
     * @Route("/tabs/{type}/{seoBaseRouteId}/{parentId}", name="tabs")
     * @Method("GET")
     */
    public function tabsAction($type, $seoBaseRouteId, $parentId = NULL)
    {

        $em = $this->getDoctrine()->getManager();

        $entity=NULL;
        $seoBaseRoute = $em->getRepository('SeoBundle:SeoBaseRoute')->find($seoBaseRouteId);

        $entityName = $seoBaseRoute->getEntityName();
        if($parentId){
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

            $entity = $em->getRepository("$bundle:$entityName")->find($parentId);
        }
        $imageSetting = $em->getRepository("MediaBundle:ImageSetting")->findOneBy(['entityName' => $entityName]);


        return $this->render('cms/admin/dashboard/tabs.html.twig', [
            'type' => $type,
            'entity' => $entity,
            'imageSetting' => $imageSetting,
            'entityName' => $entityName,

        ]);
    }

}
