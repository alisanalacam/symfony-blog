<?php
namespace AppBundle\Controller\Admin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Blog\Post;
/**
 * Blog içerikleri yönetmek için kullanılır
 *
 * @Route("/admin/post")
 * @Security("has_role('ROLE_ADMIN')")
 */
class BlogController extends Controller
{
    /**
     * Tüm makaleleri listeler
     *
     * @Route("/", name="admin_index")
     * @Route("/", name="admin_post_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $posts = $entityManager->getRepository('AppBundle:Blog\Post')->findAll();
        return $this->render('admin/blog/index.html.twig', array('posts' => $posts));
    }
    /**
     * Yeni Makale oluşturur
     *
     * @Route("/new", name="admin_post_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $post = new Post();
        $post->setAuthor($this->getUser()->getId());

        $form = $this->createForm('AppBundle\Form\PostType', $post)
            ->add('saveAndCreateNew', 'Symfony\Component\Form\Extension\Core\Type\SubmitType')
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug($this->get('slugger')->slugify($post->getTitle()));
            $entityManager = $this->getDoctrine()->getManager();
            var_dump($post->getAuthor());
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash('success', 'Makale başarıyla oluşturuldu');
            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('admin_post_new');
            }
            return $this->redirectToRoute('admin_post_index');
        }

        return $this->render('admin/blog/new.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * Makale detayı
     *
     * @Route("/{id}", requirements={"id" = "\d+"}, name="admin_post_show")
     * @Method("GET")
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Post $post)
    {
        if (null === $this->getUser() || !$post->isAuthor($this->getUser())) {
            throw $this->createAccessDeniedException('Posts can only be shown to their authors.');
        }
        $deleteForm = $this->createDeleteForm($post);
        return $this->render('admin/blog/show.html.twig', array(
            'post'        => $post,
            'delete_form' => $deleteForm->createView(),
        ));
    }

}