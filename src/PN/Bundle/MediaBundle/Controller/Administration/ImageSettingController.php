<?php

namespace PN\Bundle\MediaBundle\Controller\Administration;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use PN\Bundle\MediaBundle\Entity\ImageSetting;
use PN\Bundle\MediaBundle\Form\ImageSettingType;

/**
 * Image controller.
 *
 * @Route("/image-setting")
 */
class ImageSettingController extends Controller
{


    /**
     * Lists all ImageSetting entities.
     *
     * @Route("/", name="imagesetting_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return $this->render('MediaBundle:Administration/ImageSetting:index.html.twig');

    }

    /**
     * Creates a new Image entity.
     *
     * @Route("/new", name="imagesetting_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {

        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $isSuperUser = $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');

        $em = $this->getDoctrine()->getManager();
        $entities = [];

        $imageSetting = new ImageSetting();
        $form = $this->createForm(ImageSettingType::class, $imageSetting, ['isSuperUser' => $isSuperUser]);
        $formOptions = $form->get('entityName')->getConfig()->getOptions();

        $meta = $em->getMetadataFactory()->getAllMetadata();
        foreach ($meta as $m) {
            if (array_key_exists('post', $m->getAssociationMappings())) {
                $entities[(new \ReflectionClass($m->getName()))->getShortName()] = (new \ReflectionClass($m->getName()))->getShortName();
            }
        }

        $formOptions['choices'] = $entities;

        $form->add('entityName', ChoiceType::class, $formOptions);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userName = $this->get('user')->getUserName();
            $imageSetting->setCreator($userName);
            $imageSetting->setModifiedBy($userName);
            $em->persist($imageSetting);
            $em->flush();

            $this->addFlash('success', 'Successfully saved');

            return $this->redirect($this->generateUrl('imagesetting_index'));
        }

        return $this->render('MediaBundle:Administration/ImageSetting:new.html.twig', [
                'entity' => $imageSetting,
                'form' => $form->createView(),
            ]
        );
    }


    /**
     * Edits an existing ImageSetting entity.
     *
     * @Route("/{id}/edit", name="imagesetting_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, ImageSetting $imageSetting)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = [];

        $isSuperUser = $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');
        $editForm = $this->createForm(ImageSettingType::class, $imageSetting, ['isSuperUser' => $isSuperUser]);
        if ($isSuperUser) {
            $formOptions = $editForm->get('entityName')->getConfig()->getOptions();

            $meta = $em->getMetadataFactory()->getAllMetadata();
            foreach ($meta as $m) {
                if (array_key_exists('post', $m->getAssociationMappings())) {
                    $entities[(new \ReflectionClass($m->getName()))->getShortName()] = (new \ReflectionClass($m->getName()))->getShortName();
                }
            }

            $formOptions['choices'] = $entities;

            $editForm->add('entityName', ChoiceType::class, $formOptions);
        }
        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $userName = $this->get('user')->getUserName();
            $imageSetting->setModifiedBy($userName);
            $em->flush();

            $this->addFlash('success', 'Successfully updated');

            return $this->redirect($this->generateUrl('imagesetting_edit', array('id' => $imageSetting->getId())));
        }

        return $this->render('MediaBundle:Administration/ImageSetting:edit.html.twig', [
            'imageSetting' => $imageSetting,
            'edit_form' => $editForm->createView(),
            ]
        );
    }


    /**
     * Lists all ImageSetting entities.
     *
     * @Route("/data/table", defaults={"_format": "json"}, name="imagesetting_datatable")
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

        $count = $em->getRepository('MediaBundle:ImageSetting')->filter($search, TRUE);
        $imageSettings = $em->getRepository('MediaBundle:ImageSetting')->filter($search, FALSE, $start, $length);

        return $this->render("MediaBundle:Administration/ImageSetting:datatable.json.twig", [
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "imageSettings" => $imageSettings,
            ]
        );
    }

}
