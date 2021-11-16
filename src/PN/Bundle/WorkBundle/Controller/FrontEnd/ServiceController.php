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
 * Service controller.
 *
 * @Route("capabilities")
 */
class ServiceController extends Controller
{

    /**
     * Lists all Service entities.
     *
     * @Route("", name="fe_service")
     * @Method("GET")
     * @Template()
     */
    public function filterAction()
    {
        $em = $this->getDoctrine()->getManager();
        $servicePage = $em->getRepository('SeoBundle:SeoPage')->find(9);

        return $this->render('work/frontEnd/service/filter.html.twig', ['servicePage' => $servicePage]);

    }


}
