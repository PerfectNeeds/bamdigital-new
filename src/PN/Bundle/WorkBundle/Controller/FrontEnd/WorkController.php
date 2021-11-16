<?php

namespace PN\Bundle\WorkBundle\Controller\FrontEnd;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PN\Bundle\CMSBundle\Lib\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Work controller.
 *
 * @Route("work")
 */
class WorkController extends Controller
{

    /**
     * Lists all work entities.
     *
     * @Route("/", name="fe_work")
     * @Method("GET")
     * @Template()
     */
    public function filterAction()
    {
        $em = $this->getDoctrine()->getManager();
        $workPage = $em->getRepository('SeoBundle:SeoPage')->find(2);

        return $this->render('work/frontEnd/work/filter.html.twig', ['workPage' => $workPage]);

    }


    /**
     * show work entity.
     *
     * @Route("/show/{slug}", name="fe_work_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        if ($slug == "halwani") {
            $workPage = $em->getRepository('SeoBundle:SeoPage')->find(3);
            return $this->render('work/frontEnd/work/halwani.html.twig',['workPage'=>$workPage]);
        } elseif ($slug == "coca-cola") {
            $workPage = $em->getRepository('SeoBundle:SeoPage')->find(4);
            return $this->render('work/frontEnd/work/cocaCola.html.twig',['workPage'=>$workPage]);
        } elseif ($slug == "formula-onederful") {
            $workPage = $em->getRepository('SeoBundle:SeoPage')->find(5);
            return $this->render('work/frontEnd/work/formulaOnederful.html.twig',['workPage'=>$workPage]);
        } elseif ($slug == "runway") {
            $workPage = $em->getRepository('SeoBundle:SeoPage')->find(6);
            return $this->render('work/frontEnd/work/runway.html.twig',['workPage'=>$workPage]);
        } elseif ($slug == "pastaweesy") {
            $workPage = $em->getRepository('SeoBundle:SeoPage')->find(7);
            return $this->render('work/frontEnd/work/pastaweesy.html.twig',['workPage'=>$workPage]);
        } elseif ($slug == "royal-ceramica") {
            $workPage = $em->getRepository('SeoBundle:SeoPage')->find(8);
            return $this->render('work/frontEnd/work/royalCeramica.html.twig',['workPage'=>$workPage]);
        } else {
            throw new NotFoundHttpException();
        }

    }


}
