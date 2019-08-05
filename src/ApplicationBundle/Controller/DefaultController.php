<?php

namespace ApplicationBundle\Controller;

use AdminBundle\Entity\Category;
use AdminBundle\Entity\Articles;
use AdminBundle\Entity\Comment;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/accueil", name="accueil")
     */
    public function indexAction(Request $request)
    {
        $categories = $this->getDoctrine()->getRepository("AdminBundle:Category")->findAll();

        return $this->render('ApplicationBundle:Default:index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/home", name="home")
     */
    public function homeAction(Request $request)
    {
        $categories = $this->getDoctrine()->getRepository("AdminBundle:Category")->findAll();
        $paginator  = $this->get('knp_paginator');
        $articles = $paginator->paginate(
            $this->getDoctrine()->getRepository("AdminBundle:Articles")->findBy(array(), array('id' => 'desc')),
            $request->query->getInt('page', 1),
            4
        );
        return $this->render('ApplicationBundle:Default:home.html.twig', [
            'categories' => $categories,
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/CatId={id}", name="homeCat")
     */
    public function homeCatAction($id)
    {
        $categories = $this->getDoctrine()->getRepository("AdminBundle:Category")->findAll();
        $categorie = $this->getDoctrine()->getRepository("AdminBundle:Category")->find($id);
        $articles = $this->getDoctrine()->getRepository("AdminBundle:Articles")->findByCategory($id, array('id'=> 'desc'));
        return $this->render('ApplicationBundle:Default:homeCat.html.twig', [
            'categories' => $categories,
            'articles' => $articles,
            'category' => $categorie
        ]);
    }

    /**
     * @Route("/home/{id}", name="read")
     */
    public function readAction($id, Request $request)
    {
        $comments = new Comment;
        $article = $this->getDoctrine()->getRepository("AdminBundle:Articles")->find($id);

        $form = $this->createFormBuilder($comments)
            ->add('author', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('content', TextareaType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary mb-3')))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $author = $form['author']->getData();
            $content = $form['content']->getData();
            $now = new \DateTime('now');

            $comments->setAuthor($author);
            $comments->setContent($content);
            $comments->setArticles($article);
            $comments->setCreatedAt($now);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comments);
            $em->flush();

            return $this->redirect($request->getUri());
        }

        $comments = $this->getDoctrine()->getRepository("AdminBundle:Comment")->findBy(array('articles' => $id));





        $categories = $this->getDoctrine()->getRepository("AdminBundle:Category")->findAll();
        return $this->render('ApplicationBundle:Default:read.html.twig', [
            'categories' => $categories,
            'articles' => $article,
            'comments' => $comments,
            'form' => $form->createView(),
        ]);
    }
}
