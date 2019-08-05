<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Users;
use AdminBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/register", name="registerpage")
     */

    public function indexAction(Request $request)
    {
        $passwordEncoder = $this->get('security.password_encoder');
        $signup = new Users;
        $form = $this->createFormBuilder($signup)
                ->add('username', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
                ->add('password',  RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => 'The password fields must match.',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'required' => true,
                    'first_options'  => ['label' => 'Password', 'attr' => array('class' => 'form-control mb-3')],
                    'second_options' => ['label' => 'Repeat Password', 'attr' => array('class' => 'form-control mb-3')],
                    ])
                ->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary mb-3')))
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $username = $form['username']->getData();

            $signup->setUsername($username);
            // Encoder le mot de passe
            $password = $passwordEncoder->encodePassword($signup, $signup->getPassword());
            // Enregistrer le mot de passe coder au database
            $signup->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($signup);
            $em->flush();

            $this->addFlash(
                'notice',
                'Welcome '.$username.' you are the new Admin of this Users.'
            );
        }

        return $this->render('AdminBundle:Default:index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/profile", name="profilepage")
     */
    public function profileAction(Request $request)
    {
        // On récupère le repository ou on a notre base de donnee
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('AdminBundle:Articles');


        // On récupère l'entité correspondante à l'id $id
        $articles = $repository->findAll();



        return $this->render('AdminBundle:Default:profile.html.twig', [
            'articles' => $articles,
            'current_menu' => 'articles'

        ]);
    }

    /**
     * @Route("/categories", name="categoriespage")
     */
    public function categoriesAction(Request $request)
    {
        // On récupère le repository ou on a notre base de donnee
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('AdminBundle:Category');


        // On récupère l'entité correspondante
        $categories = $repository->findAll();



        return $this->render('AdminBundle:Default:categories.html.twig', [
            'categories' => $categories,
            'current_menu' => 'categories'

        ]);
    }

    /**
     * @Route("/categories/delete={id}", name="categoriesDelpage")
     */
    public function categoriesDelAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository("AdminBundle:Category")->find($id);

        $em->remove($categories);
        $em->flush();

        $this->addFlash(
            'notice',
            'Category Deleted'
        );
        return $this->redirectToRoute('categoriespage');
    }

    /**
     * @Route("/categories/create", name="createCatpage")
     */

    public function createAction(Request $request)
    {
        $categories = new Category;
        $form = $this->createFormBuilder($categories)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary mb-3')))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $name = $form['name']->getData();

            $categories->setName($name);

            $em = $this->getDoctrine()->getManager();
            $em->persist($categories);
            $em->flush();

            $this->addFlash(
                'notice',
                'New Category Added'
            );

            return $this->redirectToRoute('categoriespage');
        }
        return $this->render('AdminBundle:Default:createCat.html.twig', [
            'form' => $form->createView(),
            'current_menu' => 'categories'
        ]);
    }
}
