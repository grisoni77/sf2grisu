<?php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Blogger\BlogBundle\Entity\Blog;
use Blogger\BlogBundle\Entity\Comment;
use Blogger\BlogBundle\Form\CommentType;

/**
 * Description of CommentController
 *
 * @author 71537
 */
class CommentController extends Controller {
    
    /**
     * @Route("/comment/new/{blog_id}", requirements={"blog_id" = "\d+"})
     * @Method({"GET"})
     */
    public function newAction($blog_id)
    {
        $blog = $this->getBlog($blog_id);
        
        $comment = new Comment();
        $comment->setBlog($blog);
        
        $form = $this->createForm(new CommentType(), $comment);
        
        return $this->render('BloggerBlogBundle:Comment:create.html.twig', array(
            'comment'   => $comment,
            'form'      => $form->createView()
        ));
        
    }


    /**
     * @Route("/comment/{blog_id}", requirements={"blog_id" = "\d+"})
     * @Method({"POST"})
     */
    public function createAction($blog_id) 
    {
        $blog = $this->getBlog($blog_id);
        
        $comment = new Comment();
        $comment->setBlog($blog);
        
        $request = $this->getRequest();
        $form = $this->createForm(new CommentType(), $comment);
        $form->bindRequest($request);
        
        if ($form->isValid()) 
        {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($comment);
            $em->flush();
            
            
            return $this->redirect(
                    $this->generateUrl('blogger_blog_blog_show', array('id' => $blog_id))
                    . '#comment-'.$comment->getId()
                    );
        } 
        
        return $this->render('BloggerBlogBundle:Comment:create.html.twig', array(
            'comment'   => $comment,
            'form'      => $form->createView()
        ));
    }
    
    /**
     * @param int $blog_id
     * @return Blogger\BlogBundle\Entity\Blog 
     */
    public function getBlog($blog_id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        /* @var Blogger\BlogBundle\Entity\Blog $blog */
        $blog = $em->getRepository('BloggerBlogBundle:Blog')->find($blog_id);
        return $blog;
    }

}

?>