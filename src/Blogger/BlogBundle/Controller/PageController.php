<?php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Blogger\BlogBundle\Entity\Enquiry;
use Blogger\BlogBundle\Form\EnquiryType;

class PageController extends Controller {

    /**
     * @Route("/")
     * @Method({"GET"})
     * @Template()
     */
    public function indexAction() {
        return $this->render('BloggerBlogBundle:Page:index.html.twig');
    }

    /**
     * @Route("/about")
     * @Method({"GET"})
     * @Template()
     */
    public function aboutAction() {
        return $this->render('BloggerBlogBundle:Page:about.html.twig');
    }

    /**
     * @Route("/contact")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function contactAction() {
        $enquiry = new Enquiry();
        $form = $this->createForm(new EnquiryType(), $enquiry);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                // Perform some action, such as sending an email
                $message = \Swift_Message::newInstance()
                        ->setSubject('Email di contatto')
                        ->setFrom($this->container->getParameter('blogger_blog.emails.from_email'))
                        ->setTo($this->container->getParameter('blogger_blog.emails.contact_email'))
                        ->setBody($this->renderView('BloggerBlogBundle:Page:contactEmail.txt.twig', array('enquiry' => $enquiry)));
                $this->get('mailer')->send($message);
                $this->get('session')->setFlash('blogger-notice','La tua richiesta è stata inviata con successo!');
                // Redirect - This is important to prevent users re-posting
                // the form if they refresh the page
                return $this->redirect($this->generateUrl('blogger_blog_page_contact'));
            }
        }

        return $this->render('BloggerBlogBundle:Page:contact.html.twig', array(
                    'form' => $form->createView()
                ));
    }

}