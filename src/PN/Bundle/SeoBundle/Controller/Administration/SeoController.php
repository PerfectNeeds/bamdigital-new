<?php

namespace PN\Bundle\SeoBundle\Controller\Administration;

use PN\Bundle\CMSBundle\Form\PostType;
use PN\Bundle\SeoBundle\Form\SeoType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PN\Bundle\CMSBundle\Entity\Service;
use PN\Bundle\CMSBundle\Form\ServiceType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormError;
use PN\Utils\Validate;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Seo controller.
 *
 * @Route("/")
 */
class SeoController extends Controller
{

    protected $em = null;

    public function __construct(EntityManager $em = null)
    {
        $this->em = $em;
    }

    public function validateSeo(\PN\Bundle\SeoBundle\Entity\Seo $seo)
    {
        $session = new Session();
        $return = TRUE;
        if ($seo->getId() == NULL) {
            $checkSlug = $this->em->getRepository('SeoBundle:Seo')->findBy(array('slug' => $seo->getRawSlug()));
            if (count($checkSlug) > 0) {
                $session->getFlashBag()->add('error', 'the HTML Slug is duplicated, please change it');
                $return = FALSE;
            }
        } else {
            $checkSlug = $this->em->getRepository('SeoBundle:Seo')->queryBySlugAndNotId($seo->getRawSlug(), $seo->getId());
            if (count($checkSlug) > 0 and is_array($checkSlug)) {
                $session->getFlashBag()->add('error', 'the HTML Slug is duplicated, please change it');
                $return = FALSE;
            }
        }
        return $return;
    }


    /**
     * check that focusKeyword exist only one time
     *
     * @Route("/check-focus-keyword", name="fe_check_focus_keyword_ajax")
     * @Method("GET")
     */
    public function checkFocusKeyword(Request $request)
    {

        $seoId = $request->query->get('seoId');
        $focusKeyword = $request->query->get('focusKeyword');
        $em = $this->getDoctrine()->getManager();
        $return = 0;
        if ($seoId == NULL) {
            $seo = $em->getRepository('SeoBundle:Seo')->findBy(array('focusKeyword' => $focusKeyword, 'deleted' => FALSE));
            if (count($seo) > 0) {
                $return = count($seo);
            }
        } else {
            $seo = $em->getRepository('SeoBundle:Seo')->findByFocusKeywordAndNotId($focusKeyword, $seoId);
            if (count($seo) > 0) {
                $return = count($seo);
            }
        }

        return new Response($return);
    }

    /**
     * check that Slug exist only one time
     *
     * @Route("/check-slug", name="fe_check_slug_ajax")
     * @Method("GET")
     */
    public function checkSlug(Request $request)
    {
        $seoId = $request->query->get('seoId');
        $seoBaseRouteId = $request->query->get('seoBaseRouteId');
        $slug = $request->query->get('slug');
        $em = $this->getDoctrine()->getManager();
        $return = 0;

        $seoBaseRoute = $em->getRepository('SeoBundle:SeoBaseRoute')->find($seoBaseRouteId);
        if ($seoId == NULL) {
            $seo = $em->getRepository('SeoBundle:Seo')->findBy(array('seoBaseRoute' => $seoBaseRoute->getId(), 'slug' => $slug, 'deleted' => FALSE));
            if (count($seo) > 0) {
                $return = count($seo);
            }
        } else {
            $seo = $em->getRepository('SeoBundle:Seo')->findBySlugAndBaseRouteAndNotId($slug, $seoBaseRoute->getId(), $seoId);
            if (count($seo) > 0) {
                $return = count($seo);
            }
        }

        return new Response($return);
    }


    /**
     * Displays a form to edit an existing product entity.
     *
     * @Route("/{baseId}/{id}/seo", name="seo_edit")
     * @Method({"GET", "POST"})
     */
    public function seoAction(Request $request, $baseId, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_SEO');

        $em = $this->getDoctrine()->getManager();

        $seoBaseRoute = $em->getRepository('SeoBundle:SeoBaseRoute')->find($baseId);

        $entityName = $seoBaseRoute->getEntityName();
        $meta = $em->getMetadataFactory()->getAllMetadata();
        $bundle = "";
        $post_form = NULL;
        foreach ($meta as $m) {
            $name = (new \ReflectionClass($m->getName()))->getShortName();
            if ($name == $entityName) {
                $nameSpaces = explode('\\', $m->getName());
                if (count($nameSpaces) == 5) {
                    $bundle = $nameSpaces[2];
                }
            }
        }
        $entity = $em->getRepository("$bundle:$entityName")->find($id);
        if (method_exists($entity,'getPost')) {
            $postForm = $this->createForm(PostType::class, $entity->getPost());
            $postForm->handleRequest($request);
            $post_form=$postForm->createView();
        }
        $seoForm = $this->createForm(SeoType::class, $entity->getSeo());
        $seoForm->handleRequest($request);

        if ($seoForm->isSubmitted() && $seoForm->isValid()) {
            $seo = $this->get('seo')->createOrUpdate($request, $entity);
            if ($seo) {
                $userName = $this->get('user')->getUserName();
                $entity->setModifiedBy($userName);
                $em->flush();
                $this->addFlash('success', 'Successfully updated');
                return $this->redirect($request->headers->get('referer'));
            }
        }

        $seoBaseRoute = $em->getRepository('SeoBundle:SeoBaseRoute')->findByEntity($entity);

        return $this->render('cms/admin/seo/seo.html.twig', array(
            'entity' => $entity,
            'entityName' => $entityName,
            'seo_form' => $seoForm->createView(),
            'post_form' => $post_form,
            'seoBaseRoute' => $seoBaseRoute,

        ));
    }


}
