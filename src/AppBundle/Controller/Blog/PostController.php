<?php
namespace AppBundle\Controller\Blog;

use AppBundle\Entity\Blog\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @Route("/posts/{slug}", name="blog_post")
     * @param Post $post
     * @return Response
     */
    public function postShowAction(Post $post)
    {
        return $this->render('blog/post_show.html.twig', array('post' => $post));
    }

    /**
     * @Route("/comment/{postSlug}/new", name = "comment_new")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Method("POST")
     * @ParamConverter("post", options={"mapping": {"postSlug": "slug"}})
     * @param Request $request
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function commentNewAction(Request $request, Post $post)
    {
        $form = $this->createForm('AppBundle\Form\CommentType');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Comment $comment */
            $comment = $form->getData();
            $comment->setAuthor($this->getUser());
            $comment->setPost($post);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('blog_post', array('slug' => $post->getSlug()));
        }
        return $this->render('blog/comment_form_error.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }
    /**
     * @param Post $post
     * @return Response
     */
    public function commentFormAction(Post $post)
    {
        $form = $this->createForm('AppBundle\Form\CommentType');
        return $this->render('blog/_comment_form.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }
}