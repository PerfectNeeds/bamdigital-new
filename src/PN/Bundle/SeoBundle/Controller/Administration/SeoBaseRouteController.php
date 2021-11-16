<?php

namespace PN\Bundle\SeoBundle\Controller\Administration;

use PN\Bundle\SeoBundle\Entity\SeoBaseRoute;
use PN\Bundle\SeoBundle\Form\SeoBaseRouteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Seobaseroute controller.
 *
 * @Route("seobaseroute")
 */
class SeoBaseRouteController extends Controller
{


    /**
     * Lists all seoBaseRoute entities.
     *
     * @Route("/", name="seobaseroute_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('SeoBundle:Administration/SeoBaseRoute:index.html.twig');

    }

    /**
     * Creates a new seoBaseRoute entity.
     *
     * @Route("/new", name="seobaseroute_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $em = $this->getDoctrine()->getManager();
        $entities = [];

        $seoBaseRoute = new Seobaseroute();
        $form = $this->createForm(SeoBaseRouteType::class, $seoBaseRoute);
        $formOptions = $form->get('entityName')->getConfig()->getOptions();

        $meta = $em->getMetadataFactory()->getAllMetadata();
        foreach ($meta as $m) {
            if (array_key_exists('seo', $m->getAssociationMappings())) {
                $entities[(new \ReflectionClass($m->getName()))->getShortName()] = (new \ReflectionClass($m->getName()))->getShortName();
            }
        }

        $formOptions['choices'] = $entities;

        $form->add('entityName', ChoiceType::class, $formOptions);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $userName = $this->get('user')->getUserName();
            $seoBaseRoute->setCreator($userName);
            $seoBaseRoute->setModifiedBy($userName);
            $em->persist($seoBaseRoute);
            $em->flush();

            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute('seobaseroute_index');
        }

        return $this->render('SeoBundle:Administration/SeoBaseRoute:new.html.twig', [
                'seoBaseRoute' => $seoBaseRoute,
                'form' => $form->createView(),
                'entities' => $entities,
            ]
        );
    }

    /**
     * Displays a form to edit an existing seoBaseRoute entity.
     *
     * @Route("/{id}/edit", name="seobaseroute_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, SeoBaseRoute $seoBaseRoute)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $em = $this->getDoctrine()->getManager();
        $entities = [];

        $editForm = $this->createForm(SeoBaseRouteType::class, $seoBaseRoute);
        $formOptions = $editForm->get('entityName')->getConfig()->getOptions();

        $meta = $em->getMetadataFactory()->getAllMetadata();
        foreach ($meta as $m) {
            if (array_key_exists('seo', $m->getAssociationMappings())) {
                $entities[(new \ReflectionClass($m->getName()))->getShortName()] = (new \ReflectionClass($m->getName()))->getShortName();
            }
        }

        $formOptions['choices'] = $entities;

        $editForm->add('entityName', ChoiceType::class, $formOptions);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $userName = $this->get('user')->getUserName();
            $seoBaseRoute->setModifiedBy($userName);
            $em->flush();

            $this->addFlash('success', 'Successfully updated');

            return $this->redirectToRoute('seobaseroute_edit', array('id' => $seoBaseRoute->getId()));
        }

        return $this->render('SeoBundle:Administration/SeoBaseRoute:edit.html.twig', [
                'seoBaseRoute' => $seoBaseRoute,
                'edit_form' => $editForm->createView(),
            ]
        );
    }


    /**
     * Lists all seoBaseRoute entities.
     *
     * @Route("/data/table", defaults={"_format": "json"}, name="seobaseroute_datatable")
     * @Method("GET")
     */
    public function dataTableAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $srch = $request->query->get("search");
        $start = $request->query->get("start");
        $length = $request->query->get("length");
        $ordr = $request->query->get("order");

        $search = new \stdClass;
        $search->string = $srch['value'];
        $search->ordr = $ordr[0];

        $count = $em->getRepository('SeoBundle:SeoBaseRoute')->filter($search, TRUE);
        $seoBaseRoutes = $em->getRepository('SeoBundle:SeoBaseRoute')->filter($search, FALSE, $start, $length);

        return $this->render("SeoBundle:Administration/SeoBaseRoute:datatable.json.twig", [
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "seoBaseRoutes" => $seoBaseRoutes,
            ]
        );
    }


}
