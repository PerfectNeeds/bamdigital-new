<?php

namespace PN\Bundle\CMSBundle\Controller\Administration;

use PN\Bundle\CMSBundle\Entity\Banner;
use PN\Bundle\CMSBundle\Form\BannerType;
use PN\Bundle\MediaBundle\Form\SingleImageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Banner controller.
 *
 * @Route("banner")
 */
class BannerController extends Controller
{
    /**
     * Lists all banner entities.
     *
     * @Route("/", name="banner_index")
     * @Method("GET")
     */
    public function indexAction()
    {

        return $this->render('cms/admin/banner/index.html.twig');
    }

    /**
     * Creates a new banner entity.
     *
     * @Route("/new", name="banner_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $banner = new Banner();
        $form = $this->createForm(BannerType::class, $banner);
        $form->handleRequest($request);

        $uploadForm = $this->createForm(SingleImageType::class);
        $uploadForm->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $userName = $this->get('user')->getUserName();
            $banner->setCreator($userName);
            $banner->setModifiedBy($userName);
            $em->persist($banner);
            $em->flush();

            $data_upload = $uploadForm->getData();

            $file = $data_upload["file"];

            $this->get('upload_image')->uploadSingleImage($banner, $file, 100, $request);


            $this->addFlash('success', 'Successfully saved');
            return $this->redirectToRoute('banner_index');
        }

        return $this->render('cms/admin/banner/new.html.twig', array(
            'banner' => $banner,
            'form' => $form->createView(),
            'uploadFrom'=>$uploadForm->createView()
        ));
    }


    /**
     * Displays a form to edit an existing banner entity.
     *
     * @Route("/{id}/edit", name="banner_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Banner $banner)
    {
        $editForm = $this->createForm(BannerType::class, $banner);
        $editForm->handleRequest($request);

        $uploadForm = $this->createForm(SingleImageType::class);
        $uploadForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $userName = $this->get('user')->getUserName();
            $banner->setModifiedBy($userName);

            $this->getDoctrine()->getManager()->flush();

            $data_upload = $uploadForm->getData();

            $file = $data_upload["file"];
            $this->get('upload_image')->uploadSingleImage($banner, $file, 100, $request);


            $this->addFlash('success', 'Successfully updated');
            return $this->redirectToRoute('banner_edit', array('id' => $banner->getId()));
        }

        return $this->render('cms/admin/banner/edit.html.twig', array(
            'banner' => $banner,
            'edit_form' => $editForm->createView(),
            'uploadFrom' => $uploadForm->createView(),
        ));
    }

    /**
     * Deletes a banner entity.
     *
     * @Route("/{id}", name="banner_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Banner $banner)
    {
        $em = $this->getDoctrine()->getManager();
        if ($banner->getImage()) {
            $banner->getImage()->storeFilenameForRemove('banner/image/' . $banner->getId());
            $banner->getImage()->removeUpload();
        }
        $em->remove($banner);
        $em->flush();


        return $this->redirectToRoute('banner_index');
    }

    /**
     * Lists all Banner entities.
     *
     * @Route("/data/table", defaults={"_format": "json"}, name="banner_datatable")
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

        $count = $em->getRepository('CMSBundle:Banner')->filter($search, TRUE);
        $banners = $em->getRepository('CMSBundle:Banner')->filter($search, FALSE, $start, $length);
        $placements = Banner::$placements;

        return $this->render("cms/admin/banner/datatable.json.twig", array(
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "banners" => $banners,
                "placements"=>$placements

            )
        );
    }
}
