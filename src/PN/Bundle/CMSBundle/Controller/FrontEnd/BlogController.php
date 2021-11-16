<?php

namespace PN\Bundle\CMSBundle\Controller\FrontEnd;

use PN\Bundle\CMSBundle\Entity\Blog;
use PN\Bundle\CMSBundle\Entity\BlogCategory;
use PN\Bundle\CMSBundle\Form\BlogCategoryType;
use PN\Bundle\CMSBundle\Form\BlogType;
use PN\Bundle\CMSBundle\Lib\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @Route("/{page}",requirements={"page" = "\d+"}, name="fe_blog_filter")
     * @Route("/category/{slug}", name="fe_blog_category")
     * @Method("GET")
     */
    public function filterAction(Request $request, $page = 1, $slug = NULL)
    {
        $em = $this->getDoctrine()->getManager();

        $routeName = $request->get('_route');
        $search = new \stdClass;
        $search->string = $request->query->get('str');
        $search->deleted = 0;
        $search->publish = 1;

        if ($routeName == 'fe_blog_category') {
            $category = $em->getRepository('CMSBundle:BlogCategory')->findOneBySlug($slug);
            if (!$category) {
                throw new NotFoundHttpException();
            }
            $search->category = $category->getId();
        }
        $count = $em->getRepository('CMSBundle:Blog')->filter($search, TRUE);
        $paginator = new Paginator($count, $page, 5);
        $blogs = $em->getRepository('CMSBundle:Blog')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());
        $blogPage = $em->getRepository('SeoBundle:SeoPage')->find(2);

        $blogCategories = $em->getRepository('CMSBundle:BlogCategory')->findAll();

        $bannerParams = new \stdClass;
        $bannerParams->ordr = ['dir' => NULL, 'column' => 5];
        $bannerParams->placement = 2;
        $banners = $em->getRepository('CMSBundle:Banner')->filter($bannerParams, FALSE, 0, 1);

        $banner=NULL;
        if (count($banners) > 0) {
            $banner = $banners[0];
        }

        $bannerParams->placement = 10;
        $sideBanners = $em->getRepository('CMSBundle:Banner')->filter($bannerParams, FALSE, 0, 1);

        $sideBanner=NULL;
        if (count($sideBanners) > 0) {
            $sideBanner = $sideBanners[0];
        }
        return $this->render('cms/frontEnd/blog/filter.html.twig', [
            'blogs' => $blogs,
            'search' => $search,
            'paginator' => $paginator->getPagination(),
            'blogPage' => $blogPage,
            'blogCategories' => $blogCategories,
            'banner' => $banner,
            'sideBanner' => $sideBanner

        ]);
    }


    /**
     * show Blog.
     *
     * @Route("/{slug}", name="fe_blog_show")
     * @Method("GET")
     */
    public function showAction(Request $request, $slug = NULL)
    {
        $em = $this->getDoctrine()->getManager();

        $blog = $em->getRepository('CMSBundle:Blog')->findOneBySlug($slug);
        if (!$blog) {
            throw new NotFoundHttpException();
        }

        $blogCategories = $em->getRepository('CMSBundle:BlogCategory')->findAll();
        $relatedParams = new \stdClass;
        $relatedParams->deleted = 0;
        $relatedParams->publish = 1;
        $relatedParams->ordr = ['dir' => NULL, 'column' => 4];
        $relatedParams->blogId = $blog->getId();
        $relatedParams->category = $blog->getBlogCategory()->getId();

        $relatedBlogs = $em->getRepository('CMSBundle:Blog')->filter($relatedParams, FALSE, 0, 2);

        $productParams = new \stdClass;
        $productParams->ordr = ['dir' => NULL, 'column' => 6];
        $productParams->deleted = 0;
        $productParams->publish = 1;
        $productParams->featured = 1;

        $products = $em->getRepository('ProductBundle:Product')->filter($productParams, FALSE, 0, 3);

        $bannerParams = new \stdClass;
        $bannerParams->ordr = ['dir' => NULL, 'column' => 5];
        $bannerParams->placement = 9;
        $banners = $em->getRepository('CMSBundle:Banner')->filter($bannerParams, FALSE, 0, 1);

        $banner=NULL;
        if (count($banners) > 0) {
            $banner = $banners[0];
        }

        return $this->render('cms/frontEnd/blog/show.html.twig', [
            'blog' => $blog,
            'blogCategories' => $blogCategories,
            'relatedBlogs' => $relatedBlogs,
            'products' => $products,
            'banner' => $banner,
        ]);
    }


}
