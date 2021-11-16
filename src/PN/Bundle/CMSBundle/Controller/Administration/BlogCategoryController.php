<?php

namespace PN\Bundle\CMSBundle\Controller\Administration;

use PN\Bundle\CMSBundle\Entity\BlogCategory;
use PN\Bundle\CMSBundle\Form\BlogCategoryType;
use PN\Bundle\SeoBundle\Entity\Seo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Blogcategory controller.
 *
 * @Route("blogcategory")
 */
class BlogCategoryController extends Controller
{
    /**
     * Lists all blogCategory entities.
     *
     * @Route("/", name="blogcategory_index")
     * @Method("GET")
     */
    public function indexAction()
    {

        return $this->render('cms/admin/blogCategory/index.html.twig');

    }

    /**
     * Creates a new blogCategory entity.
     *
     * @Route("/new", name="blogcategory_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();

        $blogCategory = new Blogcategory();
        $form = $this->createForm(BlogCategoryType::class, $blogCategory);
        $form->handleRequest($request);
        $seoBaseRoute = $em->getRepository('SeoBundle:SeoBaseRoute')->findByEntity($blogCategory);

        if ($form->isSubmitted() && $form->isValid()) {
            $seoEntity = new Seo();
            $seoEntity->setSlug($blogCategory->getTitle());
            $seoEntity->setTitle($blogCategory->getTitle());
            $blogCategory->setSeo($seoEntity);
            $seo = $this->get('seo')->createOrUpdate($request, $blogCategory);
            if ($seo) {
                $userName = $this->get('user')->getUserName();
                $blogCategory->setCreator($userName);
                $blogCategory->setModifiedBy($userName);
                $em->persist($blogCategory);
                $em->flush();

                $this->addFlash('success', 'Successfully saved');

                return $this->redirectToRoute('blogcategory_index');
            }
        }
        return $this->render('cms/admin/blogCategory/new.html.twig', array(
            'blogCategory' => $blogCategory,
            'form' => $form->createView(),
            'seoBaseRoute' => $seoBaseRoute,
        ));
    }

    /**
     * Displays a form to edit an existing blogCategory entity.
     *
     * @Route("/{id}/edit", name="blogcategory_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, BlogCategory $blogCategory)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(BlogCategoryType::class, $blogCategory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $seo = $this->get('seo')->createOrUpdate($request, $blogCategory);
            if ($seo) {
                $userName = $this->get('user')->getUserName();
                $blogCategory->setModifiedBy($userName);
                $em->flush();

                $this->addFlash('success', 'Successfully updated');

                return $this->redirectToRoute('blogcategory_edit', array('id' => $blogCategory->getId()));
            }
        }
        $seoBaseRoute = $em->getRepository('SeoBundle:SeoBaseRoute')->findByEntity($blogCategory);

        return $this->render('cms/admin/blogCategory/edit.html.twig', array(
            'blogCategory' => $blogCategory,
            'edit_form' => $editForm->createView(),
            'seoBaseRoute' => $seoBaseRoute,
        ));
    }

    /**
     * Deletes a blogCategory entity.
     *
     * @Route("/{id}", name="blogcategory_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, BlogCategory $blogCategory)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();
        $userName = $this->get('user')->getUserName();
        $blogCategory->setDeletedBy($userName);
        $blogCategory->setDeleted(new \DateTime(date('Y-m-d H:i:s')));
        $em->persist($blogCategory);
        $em->flush();

        return $this->redirectToRoute('blogcategory_index');
    }


    /**
     * Lists all blogCategory entities.
     *
     * @Route("/data/table", defaults={"_format": "json"}, name="blogcategory_datatable")
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

        $count = $em->getRepository('CMSBundle:BlogCategory')->filter($search, TRUE);
        $blogCategories = $em->getRepository('CMSBundle:BlogCategory')->filter($search, FALSE, $start, $length);

        return $this->render("cms/admin/blogCategory/datatable.json.twig", array(
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "blogCategories" => $blogCategories,
            )
        );
    }


}
