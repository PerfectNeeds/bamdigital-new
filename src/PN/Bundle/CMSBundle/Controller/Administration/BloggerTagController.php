<?php

namespace PN\Bundle\CMSBundle\Controller\Administration;

use PN\Bundle\CMSBundle\Entity\BloggerTag;
use PN\Bundle\CMSBundle\Form\BloggerTagType;
use PN\Bundle\SeoBundle\Entity\Seo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Bloggertag controller.
 *
 * @Route("bloggertag")
 */
class BloggerTagController extends Controller
{
    /**
     * Lists all bloggerTag entities.
     *
     * @Route("/", name="bloggertag_index")
     * @Method("GET")
     */
    public function indexAction()
    {

        return $this->render('cms/admin/bloggerTag/index.html.twig');
    }

    /**
     * Creates a new bloggerTag entity.
     *
     * @Route("/new", name="bloggertag_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $bloggerTag = new Bloggertag();
        $form = $this->createForm(BloggerTagType::class, $bloggerTag);
        $form->handleRequest($request);
        $seoBaseRoute = $em->getRepository('SeoBundle:SeoBaseRoute')->findByEntity($bloggerTag);

        if ($form->isSubmitted() && $form->isValid()) {
            if (in_array('text/javascript', $request->getAcceptableContentTypes())) {
                $seo = new Seo();
                $seo->setTitle($bloggerTag->getTitle());
                $slug=$this->get('seo')->generateSlug($bloggerTag,$bloggerTag->getTitle());
                $seo->setSlug($slug);
                $seo->setSeoBaseRoute($seoBaseRoute);
                $bloggerTag->setSeo($seo);
            }else{
                $seo = $this->get('seo')->createOrUpdate($request, $bloggerTag);
            }

            if ($seo) {
                $userName = $this->get('user')->getUserName();
                $bloggerTag->setCreator($userName);
                $bloggerTag->setModifiedBy($userName);
                $em->persist($bloggerTag);
                $em->flush();
                if (in_array('text/javascript', $request->getAcceptableContentTypes())) {
                    $return = ['error' => 0, 'message' => 'Successfully saved', 'object' => $bloggerTag->getObj()];
                    return new JsonResponse($return);
                }
                $this->addFlash('success', 'Successfully saved');

                return $this->redirectToRoute('bloggertag_index');
            }
        } elseif ($form->isSubmitted() && $form->isValid() == FALSE AND in_array('text/javascript', $request->getAcceptableContentTypes())) {
            $return = ['error' => 1, 'message' => (string)$form->getErrors(true)];
            return new JsonResponse($return);
        }

        return $this->render('cms/admin/bloggerTag/new.html.twig', array(
            'bloggerTag' => $bloggerTag,
            'form' => $form->createView(),
            'seoBaseRoute' => $seoBaseRoute,

        ));
    }


    /**
     * Displays a form to edit an existing bloggerTag entity.
     *
     * @Route("/{id}/edit", name="bloggertag_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, BloggerTag $bloggerTag)
    {
        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(BloggerTagType::class, $bloggerTag);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $seo = $this->get('seo')->createOrUpdate($request, $bloggerTag);
            if ($seo) {
                $userName = $this->get('user')->getUserName();
                $bloggerTag->setModifiedBy($userName);
                $em->flush();

                $this->addFlash('success', 'Successfully updated');

                return $this->redirectToRoute('bloggertag_edit', array('id' => $bloggerTag->getId()));
            }
        }
        $seoBaseRoute = $em->getRepository('SeoBundle:SeoBaseRoute')->findByEntity($bloggerTag);

        return $this->render('cms/admin/bloggerTag/edit.html.twig', array(
            'bloggerTag' => $bloggerTag,
            'edit_form' => $editForm->createView(),
            'seoBaseRoute' => $seoBaseRoute,

        ));
    }

    /**
     * Deletes a bloggerTag entity.
     *
     * @Route("/{id}", name="bloggertag_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, BloggerTag $bloggerTag)
    {
        $em = $this->getDoctrine()->getManager();
        $userName = $this->get('user')->getUserName();
        $bloggerTag->setDeletedBy($userName);
        $bloggerTag->setDeleted(new \DateTime(date('Y-m-d H:i:s')));
        $em->persist($bloggerTag);
        $em->flush();

        return $this->redirectToRoute('bloggertag_index');
    }


    /**
     * Lists all BloggerTag entities.
     *
     * @Route("/data/table", defaults={"_format": "json"}, name="bloggertag_datatable")
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

        $count = $em->getRepository('CMSBundle:BloggerTag')->filter($search, TRUE);
        $bloggerTags = $em->getRepository('CMSBundle:BloggerTag')->filter($search, FALSE, $start, $length);

        return $this->render("cms/admin/bloggerTag/datatable.json.twig", array(
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "bloggerTags" => $bloggerTags,
            )
        );
    }


}
