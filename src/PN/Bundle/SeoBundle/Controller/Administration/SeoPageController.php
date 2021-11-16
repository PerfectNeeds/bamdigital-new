<?php

namespace PN\Bundle\SeoBundle\Controller\Administration;

use PN\Bundle\SeoBundle\Entity\Seo;
use PN\Bundle\SeoBundle\Entity\SeoPage;
use PN\Bundle\SeoBundle\Form\SeoPageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * SeoPage controller.
 *
 * @Route("seo-page")
 */
class SeoPageController extends Controller
{
    /**
     * Lists all seoPage entities.
     *
     * @Route("/", name="seopage_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('SeoBundle:Administration/SeoPage:index.html.twig');

    }

    /**
     * Creates a new seoPage entity.
     *
     * @Route("/new", name="seopage_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $seoPage = new Seopage();
        $form = $this->createForm(SeoPageType::class, $seoPage);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $seoEntity = new Seo();
            $seoEntity->setSlug($seoPage->getTitle());
            $seoEntity->setTitle($seoPage->getTitle());
            $seoPage->setSeo($seoEntity);
            $seo = $this->get('seo')->createOrUpdate($request, $seoPage);

            if ($seo) {
                $userName = $this->get('user')->getUserName();
                $seoPage->setCreator($userName);
                $seoPage->setModifiedBy($userName);
                $em->persist($seoPage);
                $em->flush();

                $this->addFlash('success', 'Successfully saved');

                return $this->redirectToRoute('seopage_index');
            }

        }
        $seoBaseRoute = $em->getRepository('SeoBundle:SeoBaseRoute')->findByEntity($seoPage);

        return $this->render('SeoBundle:Administration/SeoPage:new.html.twig', array(
            'seoPage' => $seoPage,
            'form' => $form->createView(),
            'seoBaseRoute' => $seoBaseRoute,
        ));
    }

    /**
     * Displays a form to edit an existing seoPage entity.
     *
     * @Route("/{id}/edit", name="seopage_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, SeoPage $seoPage)
    {

        $editForm = $this->createForm(SeoPageType::class, $seoPage);
        $editForm->handleRequest($request);
        $em=$this->getDoctrine()->getManager();
        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $seo = $this->get('seo')->createOrUpdate($request, $seoPage);

            if ($seo) {
                $userName = $this->get('user')->getUserName();
                $seoPage->setModifiedBy($userName);
                $em->flush();

                $this->addFlash('success', 'Successfully updated');

                return $this->redirectToRoute('seopage_edit', array('id' => $seoPage->getId()));
            }
        }

        $seoBaseRoute = $em->getRepository('SeoBundle:SeoBaseRoute')->findByEntity($seoPage);

        return $this->render('SeoBundle:Administration/SeoPage:edit.html.twig', array(
            'seoPage' => $seoPage,
            'edit_form' => $editForm->createView(),
            'seoBaseRoute' => $seoBaseRoute,
        ));
    }

    /**
     * Deletes a seoPage entity.
     *
     * @Route("/{id}", name="seopage_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, SeoPage $seoPage)
    {
            $em = $this->getDoctrine()->getManager();
            $em->remove($seoPage);
            $em->flush();

        return $this->redirectToRoute('seopage_index');
    }


    /**
     * Lists all seoPage entities.
     *
     * @Route("/data/table", defaults={"_format": "json"}, name="seopage_datatable")
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

        $count = $em->getRepository('SeoBundle:SeoPage')->filter($search, TRUE);
        $seoPages = $em->getRepository('SeoBundle:SeoPage')->filter($search, FALSE, $start, $length);

        return $this->render("SeoBundle:Administration/SeoPage:datatable.json.twig", array(
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "seoPages" => $seoPages,
            )
        );
    }

}
