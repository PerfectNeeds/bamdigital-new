<?php

namespace PN\Bundle\SeoBundle\Service;

use PN\Bundle\SeoBundle\Entity\Seo;
use PN\Bundle\SeoBundle\Entity\SeoSocial;
use PN\Utils\Validate;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Container;
use PN\Utils\General;

class SeoService
{

    protected $entityManager;
    protected $context;
    protected $router;
    protected $container;

    public function __construct($entityManager, Router $router, Container $container)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->container = $container;
    }

    public function createOrUpdate(Request $request, $entity)
    {

        $em = $this->entityManager;
        $seoData = $request->request->get('seo');
        $seoSocialDatas = $request->request->get('seoSocial');
        $seoEntity = $entity->getSeo();
        if ($entity->getId() == NULL) { // new
            $seoBaseRoute = $em->getRepository('SeoBundle:SeoBaseRoute')->findByEntity($entity);
            $seoEntity->setSeoBaseRoute($seoBaseRoute);
        }

        $seoEntity->setState($seoData['state']);
        if(count($seoSocialDatas) >0){
            foreach ($seoSocialDatas as $key => $seoSocialData) {
                $seoSocial = $seoEntity->getSeoSocialByType($key);
                if (Validate::not_null($seoSocialData['title']) OR Validate::not_null($seoSocialData['imageUrl']) OR Validate::not_null($seoSocialData['description'])) {
                    if (!$seoSocial) {
                        $seoSocial = new SeoSocial();
                        $seoSocial->setSocialNetwork($key);
                    }
                    $seoSocial->setTitle($seoSocialData['title']);
                    $seoSocial->setDescription($seoSocialData['description']);
                    $seoSocial->setImageUrl($seoSocialData['imageUrl']);
                    $seoSocial->setSeo($seoEntity);
                    $em->persist($seoSocial);
                } else { //remove
                    if ($seoSocial) {
                        $em->remove($seoSocial);
                    }
                }
            }
        }

        if (!Validate::not_null($seoEntity->getSlug())) {
            $request->getSession()->getFlashBag()->add('error', 'you must enter the slug');
            return FALSE;
        }
        if (!Validate::not_null($seoEntity->getTitle())) {
            $request->getSession()->getFlashBag()->add('error', 'you must enter the HTML title');
            return FALSE;
        }

        if ($entity->getId() != NULL) { // edit
            $checkSlug = $em->getRepository('SeoBundle:Seo')->findBySlugAndBaseRouteAndNotId( $seoEntity->getSlug(), $seoEntity->getSeoBaseRoute()->getId(), $seoEntity->getId());
            if (count($checkSlug) > 0) {
                $request->getSession()->getFlashBag()->add('error', 'the HTML Slug is duplicated, please change it');
                return FALSE;
            }
        } else { // new
            $seoBaseRoute = $em->getRepository('SeoBundle:SeoBaseRoute')->findByEntity($entity);

            $checkSlug = $em->getRepository('SeoBundle:Seo')->findBy(['slug' => $seoEntity->getSlug(), 'seoBaseRoute' => $seoBaseRoute->getId(), 'deleted'=> FALSE]);
            if (count($checkSlug) > 0) {
                $request->getSession()->getFlashBag()->add('error', 'the HTML Slug is duplicated, please change it');
                return FALSE;
            }

        }

        return $entity;
    }

    public function generateSlug($entity, $mainTitle)
    {
        $em = $this->entityManager;
        $seoBaseRoute = $em->getRepository('SeoBundle:SeoBaseRoute')->findByEntity($entity);

        $i = 0;
        do {
            if ($i == 0) {
                $slug = General::seoUrl($mainTitle);
            } else {
                $slug = General::seoUrl($mainTitle . '-' . substr(number_format(time() * rand(), 0, '', ''), 0, 6));
            }
            $checkSlug = $em->getRepository('SeoBundle:Seo')->findBy(['slug' => $slug, 'seoBaseRoute' => $seoBaseRoute->getId()]);
            $i++;
        } while (count($checkSlug) > 0);
        return $slug;
    }
}
