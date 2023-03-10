<?php

namespace App\Controller;

use DateTime;
use App\Entity\Topic;
use App\Form\TopicType;
use App\Entity\Categorie;
use App\Entity\Utilisateur;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class TopicController extends AbstractController
{
    //<---------- INDEXATION DES TOPICS PAR ORDRE CHRONOLOGIQUE ---------->
    #[Route('/topic', name: 'app_topic')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $topics = $doctrine->getRepository(Topic::class)->findBy([], ["dateTopic" => "ASC"]);
        return $this->render(
            'topic/index.html.twig',
            [
                'topics' => $topics,
            ]
        );
    }

    //<---------- FONCTION AJOUTER ET EDITER UN TOPIC ---------->
    #[Route("topic/add/{idcat}", name:"add_topic")]
    #[Route("topic/edit/{idcat}/{id}", name:"edit_topic")]
    #[ParamConverter("topic", options: ["mapping" => ["id" => "id"]])]
    #[ParamConverter("categorie", options: ["mapping" => ["idcat" => "id"]])]

    public function add(ManagerRegistry $doctrine, Categorie $categorie = null, Topic $topic = null, Request $request): Response 
    {
        if(!$topic) 
        {
            $topic = new Topic();
        }
        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);
        $entityManager = $doctrine->getManager();

        //<---------- SI LE FORMULAIRE EST SOUMIS ET VALIDE ---------->
        if($form->isSubmitted() && $form->isValid()) 
        {
            //<---------- RECUPERE ET STOCKE LES DONNEES DU FORMULAIRE ---------->
            $topic = $form->getData();
            //<---------- ON ASSOCIE L'UTILISATEUR CONNECTE AU TOPIC ---------->
            $topic->setUtilisateur($this->getUser());            
            //<---------- ON AJOUTE LE TOPIC DANS LA CATEGORIE EN COURS ---------->
            $categorie->addTopic($topic);
            //<---------- PREPARE ---------->
            $entityManager->persist($topic);
            //<---------- EXECUTE ---------->
            $entityManager->flush();
            $topics =$topic->getCategorie()->getId();
            return $this->redirectToRoute('show_categorie', ['id' => $topics]);
        }
        //<---------- RENVOI L'AFFICHAGE DU FORMULAIRE ---------->
        return $this->render('topic/add.html.twig', 
        [
            //<---------- CREATION DE LA VUE DU FORMULAIRE ---------->
            'formAddTopic' =>$form->createView(),
            //<---------- ID POUR EDITER LE topic ---------->
            'edit' => $topic->getId()
        ]);
        
    }

    //<---------- FONCTION SUPPRIMER UN TOPIC ---------->
    #[Route("/topic/delTopic/{id}", name: "delTopic")]
    public function deltopic(ManagerRegistry $doctrine, Topic $topic)
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($topic);
        $entityManager->flush();
        return $this->redirectToRoute('show_categorie', ['id' => $topic->getCategorie()->getId()]);
    }

    //<---------- FONCTION AFFICHER TOPIC ---------->
    #[Route("/topic/{id}", name: "show_topic")]
    public function show(Topic $topic): Response
    {
        return $this->render(
            'topic/show.html.twig',
            [
                'topic' => $topic,
            ]
        );
    }
}
