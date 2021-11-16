<?php

namespace PN\Bundle\CMSBundle\Controller\FrontEnd;

use PN\Bundle\CMSBundle\Entity\Blog;
use PN\Bundle\CMSBundle\Entity\BlogCategory;
use PN\Bundle\CMSBundle\Form\BlogCategoryType;
use PN\Bundle\CMSBundle\Form\BlogType;
use PN\Bundle\CMSBundle\Lib\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Banner controller.
 *
 * @Route("banner")
 */
class BannerController extends Controller
{

    /**
     * Lists all Banner entities.
     *
     * @Route("/{placement}", name="fe_banner")
     * @Method("GET")
     */
    public function BannerAction($placement)
    {
        $em = $this->getDoctrine()->getManager();

        $search = new \stdClass;
        $search->ordr = ['dir' => NULL, 'column' => 5];
        $search->placement = $placement;
        $banners = $em->getRepository('CMSBundle:Banner')->filter($search, FALSE, 0, 2);

        return $this->render('cms/frontEnd/banner/banner.html.twig', [
            'banners' => $banners,
        ]);
    }


}
