<?php
namespace AppBundle\Controller\Blog;

use AppBundle\Entity\Blog\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/blog")
 */
class PostController extends Controller
{
    /**
     * @Route("/", name="blog_index", defaults={"page" = 1})
     * @Route("/page/{page}", name="blog_index_paginated", requirements={"page" : "\d+"})
     * @param $page
     * @return Response
     */
    public function indexAction($page)
    {
        $query = $this->getDoctrine()->getRepository('AppBundle:Blog\Post')->queryLatest();
        $paginator = $this->get('knp_paginator');
        $posts = $paginator->paginate($query, $page, Post::NUM_ITEMS);
        $posts->setUsedRoute('blog_index_paginated');

        return $this->render('blog/index.html.twig', array('posts' => $posts));
    }
}