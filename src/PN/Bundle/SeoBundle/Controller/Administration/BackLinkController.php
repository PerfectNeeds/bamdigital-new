<?php

namespace PN\Bundle\SeoBundle\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PN\Bundle\SeoBundle\Entity\BackLink;
use PN\Bundle\SeoBundle\Form\BackLinkType;

/**
 * BackLink controller.
 *
 * @Route("/back-link")
 */
class BackLinkController extends Controller {

    /**
     * Lists all BackLink entities.
     *
     * @Route("/", name="backlink")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('SeoBundle:BackLink')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new BackLink entity.
     *
     * @Route("/", name="backlink_create")
     * @Method("POST")
     * @Template("SeoBundle:Administration/BackLink:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new BackLink();
        $form = $this->createForm(new BackLinkType(), $entity);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backlink_edit', array('id' => $entity->getId())));
        }


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new BackLink entity.
     *
     * @Route("/new", name="backlink_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new BackLink();
        $form = $this->createForm(BackLinkType::class, $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing BackLink entity.
     *
     * @Route("/{id}/edit", name="backlink_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction(BackLink $backLink) {

        $editForm = $this->createForm(BackLinkType::class, $backLink);

        return array(
            'entity' => $backLink,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing BackLink entity.
     *
     * @Route("/{id}", name="backlink_update")
     * @Method("PUT")
     * @Template("SeoBundle:Administration/BackLink:edit.html.twig")
     */
    public function updateAction(Request $request, BackLink $backLink) {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(BackLinkType::class, $backLink);
        $editForm->bind($request);


        if ($editForm->isValid()) {
            $em->persist($backLink);
            $em->flush();
            return $this->redirect($this->generateUrl('backlink'));
        }

        return array(
            'entity' => $backLink,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a BackLink entity.
     *
     * @Route("/delete", name="backlink_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request) {
        $id = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SeoBundle:BackLink')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BackLink entity.');
        }

        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('backlink'));
    }

}
