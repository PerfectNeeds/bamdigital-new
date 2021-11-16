<?php

namespace PN\Bundle\CMSBundle\Controller\FrontEnd;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PN\Bundle\CMSBundle\Lib\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * HomePage controller.
 *
 * @Route("")
 */
class HomePageController extends Controller {

    /**
     * Lists all Home entities.
     *
     * @Route("/", name="fe_home")
     * @Method("GET")
     * @Template()
     */
    public function homeAction() {
        $em = $this->getDoctrine()->getManager();
        $homePage = $em->getRepository('SeoBundle:SeoPage')->find(1);

        return $this->render('cms/frontEnd/homePage/home.html.twig', ['homePage' => $homePage]);
    }

    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/about", name="fe_about")
     * @Method("GET")
     */
    public function aboutAction() {
        $em = $this->getDoctrine()->getManager();
        $about = $em->getRepository('SeoBundle:SeoPage')->find(10);

        return $this->render('cms/frontEnd/homePage/about.html.twig', array(
                    'about' => $about,
        ));
    }

    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/client", name="fe_client")
     * @Method("GET")
     */
    public function clientAction() {
        $em = $this->getDoctrine()->getManager();
        $about = $em->getRepository('SeoBundle:SeoPage')->find(12);

        return $this->render('cms/frontEnd/homePage/client.html.twig', array(
                    'about' => $about,
        ));
    }

}
