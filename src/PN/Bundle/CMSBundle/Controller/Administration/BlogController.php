<?php

namespace PN\Bundle\CMSBundle\Controller\Administration;

use PN\Bundle\CMSBundle\Entity\Blog;
use PN\Bundle\CMSBundle\Entity\BlogCategory;
use PN\Bundle\CMSBundle\Form\BlogCategoryType;
use PN\Bundle\CMSBundle\Form\BlogType;
use PN\Bundle\SeoBundle\Entity\Seo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Blog controller.
 *
 * @Route("blog")
 */
class BlogController extends Controller
{


    /**
     * Lists all blog entities.
     *
     * @Route("/", name="blog_index")
     * @Method("GET")
     */
    public function indexAction()
    {

        return $this->render('cms/admin/blog/index.html.twig');
    }


    /**
     * Creates a new blog entity.
     *
     * @Route("/new", name="blog_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();

        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $seoEntity = new Seo();
            $seoEntity->setSlug($blog->getTitle());
            $seoEntity->setTitle($blog->getTitle());
            $blog->setSeo($seoEntity);
            $seo = $this->get('seo')->createOrUpdate($request, $blog);
            if ($seo) {
                $userName = $this->get('user')->getUserName();
                $blog->setCreator($userName);
                $blog->setModifiedBy($userName);

                $em->persist($blog);
                $em->flush();

                $this->addFlash('success', 'Successfully saved');

                return $this->redirectToRoute('post_set_images', array(
                        'id' => $blog->getPost()->getId(),
                        'pageType' => 3,
                        'parentId' => $blog->getId()
                    )
                );
            }
        }
        $seoBaseRoute = $em->getRepository('SeoBundle:SeoBaseRoute')->findByEntity($blog);

        return $this->render('cms/admin/blog/new.html.twig', array(
            'blog' => $blog,
            'form' => $form->createView(),
            'seoBaseRoute' => $seoBaseRoute,
        ));
    }

    /**
     * Displays a form to edit an existing blog entity.
     *
     * @Route("/{id}/edit", name="blog_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Blog $blog)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(BlogType::class, $blog);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $seo = $this->get('seo')->createOrUpdate($request, $blog);
            if ($seo) {
                $userName = $this->get('user')->getUserName();
                $blog->setModifiedBy($userName);
                $em->flush();

                $this->addFlash('success', 'Successfully updated');
                return $this->redirectToRoute('blog_edit', array('id' => $blog->getId()));
            }
        }
        $seoBaseRoute = $em->getRepository('SeoBundle:SeoBaseRoute')->findByEntity($blog);

        return $this->render('cms/admin/blog/edit.html.twig', array(
            'blog' => $blog,
            'edit_form' => $editForm->createView(),
            'seoBaseRoute' => $seoBaseRoute,
        ));
    }

    /**
     * Deletes a blog entity.
     *
     * @Route("/{id}", name="blog_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Blog $blog)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();
        $userName = $this->get('user')->getUserName();
        $blog->setDeletedBy($userName);
        $blog->setDeleted(new \DateTime(date('Y-m-d H:i:s')));
        $em->persist($blog);
        $em->flush();

        return $this->redirectToRoute('blog_index');
    }

    /**
     * Lists all blog entities.
     *
     * @Route("/data/table", defaults={"_format": "json"}, name="blog_datatable")
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

        $count = $em->getRepository('CMSBundle:Blog')->filter($search, TRUE);
        $blogs = $em->getRepository('CMSBundle:Blog')->filter($search, FALSE, $start, $length);

        return $this->render("cms/admin/blog/datatable.json.twig", array(
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "blogs" => $blogs,
            )
        );
    }


}
