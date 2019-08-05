<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Articles;
use AdminBundle\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CrudController extends Controller
{
    /**
     * @Route("/profile/create", name="createpage")
     */

    public function createAction(Request $request)
    {
        $article = new Articles;
        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('category', EntityType::class, array('class' => Category::class, 'choice_label' => 'name', 'attr' => array('class' => 'form-control mb-3')))
            ->add('imageFile', FileType::class, array('required' => 'false','attr' => array('class' => 'form-control')))
            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary mb-3')))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $title = $form['title']->getData();
            $cat = $form['category']->getData();
            $description = $form['description']->getData();
            $now = new \DateTime('now');

            $article->setTitle($title);
            $article->setDescription($description);
            $article->setCategory($cat);
            $article->setCreateAt($now);

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->addFlash(
                'notice',
                'New Article Added'
            );

            return $this->redirectToRoute('profilepage');
        }
        return $this->render('AdminBundle:Default:create.html.twig', [
            'form' => $form->createView(),
            'current_menu' => 'articles'
        ]);
    }

    /**
     * @Route("/profile/edit/{id}", name="editpage")
     */

    public function editAction($id, Request $request){

        $articles = $this->getDoctrine()->getRepository("AdminBundle:Articles")->find($id);

        $articles->setTitle($articles->getTitle());
        $articles->setCategory($articles->getCategory());
        $articles->setImageFile($articles->getImageFile());
        $articles->setDescription($articles->getDescription());
        $articles->setCreateAt($articles->getCreateAt());

        $form = $this->createFormBuilder($articles)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('category', EntityType::class, array('class' => Category::class, 'choice_label' => 'name', 'attr' => array('class' => 'form-control mb-3')))
            ->add('imageFile', FileType::class, array('required' => false ,'attr' => array('class' => 'form-control')))
            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary mb-3')))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $title = $form['title']->getData();
            $cat = $form['category']->getData();
            $description = $form['description']->getData();
            $imageFile = $form['imageFile']->getData();
            $now = new \DateTime('now');

            $em = $this->getDoctrine()->getManager();
            $articles= $em->getRepository('AdminBundle:Articles')->find($id);

            $articles->setTitle($title);
            $articles->setCategory($cat);
            $articles->setDescription($description);
            $articles->setImageFile($imageFile);
            $articles->setCreateAt($now);

            $em->flush();

            $this->addFlash(
                'notice',
                'Article Updated'
            );

            return $this->redirectToRoute('profilepage');
        }

        return $this->render('AdminBundle:Default:edit.html.twig', array(
            "articles" => $articles,
            "form" => $form->createView(),
            'current_menu' => 'articles'
        ));
    }

    /**
     * @Route("/profile/delete/{id}", name="deletepage")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository("AdminBundle:Articles")->find($id);

        $em->remove($articles);
        $em->flush();

        $this->addFlash(
            'notice',
            'Article Deleted'
        );
        return $this->redirectToRoute('profilepage');
    }

    /**
     * @Route("/profile/view/{id}", name="viewpage")
     */
    public function ViewAction($id)
    {
        $articles = $this->getDoctrine()->getRepository("AdminBundle:Articles")->find($id);
        return $this->render('AdminBundle:Default:view.html.twig', array(
            "articles" => $articles,
            'current_menu' => 'articles'
        ));
    }
}
