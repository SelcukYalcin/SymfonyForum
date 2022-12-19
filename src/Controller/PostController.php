<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Topic;
use App\Form\PostType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    #[Route('/post', name: 'app_post')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $posts = $doctrine->getRepository(Post::class)->findBy([],["datePub"=>"ASC"]);   
        return $this->render('post/index.html.twig',
        [
            'posts' => $posts,
        ]);
    }

        //<---------- FONCTION AJOUTER ET EDITER UN POST ---------->
        #[Route("/post/add", name:"add_post")]
        #[Route("/post/{id}/edit", name:"edit_post")]    
        public function add(ManagerRegistry $doctrine, Post $post = null, Request $request): Response 
        {
            if(!$post) 
            {
                $post = new Post();
                $post->setDatePost(new \DateTime('now'));
            }
            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);
            //<---------- SI LE FORMULAIRE EST SOUMIS ET VALIDE ---------->
            if($form->isSubmitted() && $form->isValid()) 
            {
                //  RECUPERE ET STOCKE LES DONNEES DU FORMULAIRE
                $post = $form->getData();
                $entityManager = $doctrine->getManager();
                //<---------- PREPARE ---------->
                $entityManager->persist($post);
                //<---------- EXECUTE ---------->
                $entityManager->flush();
                $posts =$post->getTopic()->getId();
                return $this->redirectToRoute('show_topic', ['id' => $posts]);
            }
            //<---------- RENVOI L'AFFICHAGE DU FORMULAIRE ---------->
            return $this->render('post/add.html.twig', 
            [
                //<---------- CREATION DE LA VUE DU FORMULAIRE ---------->
                'formAddPost' =>$form->createView(),
                //<---------- ID POUR EDITER LE POST ---------->
                'edit' => $post->getId()
            ]);
        }

    // public function addPost(ManagerRegistry $doctrine, Topic $Topic, Post $post)
    // {

    //     $entitytManager = $doctrine->getManager();
    //     $Topic->addPost($post);
    //     $entitytManager->persist($Topic);
    //     $entitytManager->flush();

    //     return $this->redirectToRoute('show_Topic', ['id' => $Topic->getId()]);
    // }
    
        //<---------- FONCTION SUPPRIMER UN POST ---------->
        #[Route("/post/{id}/delPost", name:"delPost")]
        public function delPost(ManagerRegistry $doctrine, Post $post)
        {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
            $posts =$post->getTopic()->getId();
            return $this->redirectToRoute('show_topic', ['id' => $posts]);        }
    
        //<---------- FONCTION AFFICHER POST ---------->
        #[Route('/post/{id}', name: 'show_post')]
        public function show(Post $post): Response
        {       
            return $this->render('post/show.html.twig', [
               'post' => $post,
            ]);
        }
}
