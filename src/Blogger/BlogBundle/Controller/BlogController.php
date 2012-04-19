<?php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of BlogController
 *
 * @author 71537
 */
class BlogController extends Controller {

    /**
     * @Route("/") 
     * @Method({"GET"})
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('BloggerBlogBundle:Blog');
        $blogs = $repo->getLatestBlogs();

        return $this->render('BloggerBlogBundle:Blog:index.html.twig', array(
                    'blogs' => $blogs,
                ));
    }

    /**
     * @Route("/blog/{id}", requirements={"id" = "\d+"})
     * @Method({"GET"})
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $blog = $em->getRepository('BloggerBlogBundle:Blog')->find($id);
        $comments = $em->getRepository('BloggerBlogBundle:Comment')
                ->getCommentsForBlog($blog->getId());
        if (!$blog) {
            throw $this->createNotFoundException('Impossibile trovare la risorsa specificata: ' . $id);
        }
        return $this->render('BloggerBlogBundle:Blog:show.html.twig', array(
                    'blog' => $blog,
                    'comments' => $comments,
                ));
    }

    public function sidebarAction() {
        $em = $this->getDoctrine()
                ->getEntityManager();

        $tags = $em->getRepository('BloggerBlogBundle:Blog')
                ->getTags();

        $tagWeights = $em->getRepository('BloggerBlogBundle:Blog')
                ->getTagWeights($tags);

        $latestComments = $em->getRepository('BloggerBlogBundle:Comment')
                ->getLatestComments(5);
        
        return $this->render('BloggerBlogBundle:Page:sidebar.html.twig', array(
                    'tags' => $tagWeights,
                    'latestComments' => $latestComments,
                ));
    }

}

?>