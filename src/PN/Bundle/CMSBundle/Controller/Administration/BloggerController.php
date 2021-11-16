<?php

namespace PN\Bundle\CMSBundle\Controller\Administration;

use PN\Bundle\CMSBundle\Entity\Blogger;
use PN\Bundle\CMSBundle\Entity\BloggerTag;
use PN\Bundle\CMSBundle\Form\BloggerTagType;
use PN\Bundle\CMSBundle\Form\BloggerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Blogger controller.
 *
 * @Route("blogger")
 */
class BloggerController extends Controller
{
    /**
     * Lists all blogger entities.
     *
     * @Route("/", name="blogger_index")
     * @Method("GET")
     */
    public function indexAction()
    {

        return $this->render('cms/admin/blogger/index.html.twig');
    }

    /**
     * Creates a new blogger entity.
     *
     * @Route("/new", name="blogger_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $blogger = new Blogger();
        $form = $this->createForm(BloggerType::class, $blogger);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $seo = $this->get('seo')->createOrUpdate($request, $blogger);
            if ($seo) {
                $userName = $this->get('user')->getUserName();
                $blogger->setCreator($userName);
                $blogger->setModifiedBy($userName);

                $em->persist($blogger);
                $em->flush();

                $this->addFlash('success', 'Successfully saved');

                return $this->redirectToRoute('blogger_index');
            }
        }
        $seoBaseRoute = $em->getRepository('SeoBundle:SeoBaseRoute')->findByEntity($blogger);
        $bloggerTag = new BloggerTag();
        $bloggerTagForm = $this->createForm(BloggerTagType::class, $bloggerTag, [
            'action' => $this->generateUrl('bloggertag_new')
        ]);
        $bloggerTagForm->remove('seo');

        return $this->render('cms/admin/blogger/new.html.twig', array(
            'blogger' => $blogger,
            'form' => $form->createView(),
            'bloggerTag_form' => $bloggerTagForm->createView(),
            'seoBaseRoute' => $seoBaseRoute,

        ));
    }


    /**
     * Displays a form to edit an existing blogger entity.
     *
     * @Route("/{id}/edit", name="blogger_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Blogger $blogger)
    {
        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(BloggerType::class, $blogger);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $seo = $this->get('seo')->createOrUpdate($request, $blogger);
            if ($seo) {
                $userName = $this->get('user')->getUserName();
                $blogger->setModifiedBy($userName);
                $em->flush();

                $this->addFlash('success', 'Successfully updated');
                return $this->redirectToRoute('blogger_edit', array('id' => $blogger->getId()));
            }
        }
        $seoBaseRoute = $em->getRepository('SeoBundle:SeoBaseRoute')->findByEntity($blogger);
        $bloggerTag = new BloggerTag();
        $bloggerTagForm = $this->createForm(BloggerTagType::class, $bloggerTag, [
            'action' => $this->generateUrl('bloggertag_new')
        ]);
        $bloggerTagForm->remove('seo');
        return $this->render('cms/admin/blogger/edit.html.twig', array(
            'blogger' => $blogger,
            'edit_form' => $editForm->createView(),
            'bloggerTag_form' => $bloggerTagForm->createView(),
            'seoBaseRoute' => $seoBaseRoute,

        ));
    }

    /**
     * Deletes a blogger entity.
     *
     * @Route("/{id}", name="blogger_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Blogger $blogger)
    {
        $em = $this->getDoctrine()->getManager();
        $userName = $this->get('user')->getUserName();
        $blogger->setDeletedBy($userName);
        $blogger->setDeleted(new \DateTime(date('Y-m-d H:i:s')));
        $em->persist($blogger);
        $em->flush();

        return $this->redirectToRoute('blogger_index');
    }

    /**
     * Lists all Blogger entities.
     *
     * @Route("/data/table", defaults={"_format": "json"}, name="blogger_datatable")
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
        $search->deleted = 0;

        $count = $em->getRepository('CMSBundle:Blogger')->filter($search, TRUE);
        $bloggers = $em->getRepository('CMSBundle:Blogger')->filter($search, FALSE, $start, $length);

        return $this->render("cms/admin/blogger/datatable.json.twig", array(
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "bloggers" => $bloggers,
            )
        );
    }


}
