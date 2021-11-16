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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Faq controller.
 *
 * @Route("faq")
 */
class FaqController extends Controller
{

    /**
     * Lists all blog entities.
     *
     * @Route("/{page}",requirements={"page" = "\d+"}, name="fe_faq")
     * @Method("GET")
     */
    public function filterAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $search = new \stdClass;

        $count = $em->getRepository('CMSBundle:Faq')->filter($search, TRUE);
        $paginator = new Paginator($count, $page, 5);
        $faqs = $em->getRepository('CMSBundle:Faq')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());
        $faqPage = $em->getRepository('SeoBundle:SeoPage')->find(6);

        $bannerParams = new \stdClass;
        $bannerParams->ordr = ['dir' => NULL, 'column' => 5];
        $bannerParams->placement = 12;
        $banners = $em->getRepository('CMSBundle:Banner')->filter($bannerParams, FALSE, 0, 1);

        $banner = NULL;
        if (count($banners) > 0) {
            $banner = $banners[0];
        }

        return $this->render('cms/frontEnd/faq/index.html.twig', [
            'faqs' => $faqs,
            'search' => $search,
            'paginator' => $paginator->getPagination(),
            'faqPage' => $faqPage,
            'banner' => $banner,
        ]);
    }




}
