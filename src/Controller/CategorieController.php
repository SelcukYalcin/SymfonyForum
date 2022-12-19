<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{
    // INDEXATION DES CATEGORIES PAR ORDRE ALPHABETIQUE
    #[Route('/categorie', name: 'app_categorie')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $categories  = $doctrine->getRepository(Categorie::class)->findBy([],["libelle"=>"ASC"]);
        return $this->render('categorie/index.html.twig', 
        [
            'categories' => $categories
        ]);
    }
    //<---------- FONCTION AJOUTER ET EDITER UNE CATEGORIE ---------->
    #[Route("/categorie/add", name:"add_categorie")]
    #[Route("/categorie/{id}/edit", name:"edit_categorie")]    
    public function add(ManagerRegistry $doctrine, Categorie $categorie = null, Request $request): Response 
    {
        if(!$categorie) 
        {
            $categorie = new Categorie();
        }
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        //<---------- SI LE FORMULAIRE EST SOUMIS ET VALIDE ---------->
        if($form->isSubmitted() && $form->isValid()) 
        {
            //<---------- RECUPERE ET STOCKE LES DONNEES DU FORMULAIRE ---------->
            $categorie = $form->getData();
            $entityManager = $doctrine->getManager();
            //<---------- PREPARE ---------->
            $entityManager->persist($categorie);
            //<---------- EXECUTE ---------->
            $entityManager->flush();
            return $this->redirectToRoute('app_categorie');
        }
        //<---------- RENVOI L'AFFICHAGE DU FORMULAIRE ---------->
        return $this->render('categorie/add.html.twig', 
        [
            //<---------- CREATION DE LA VUE DU FORMULAIRE ---------->
            'formAddCategorie' =>$form->createView(),
            //<---------- ID POUR EDITER LA CATEGORIE ---------->
            'edit' => $categorie->getId()
        ]);
    }

    //<---------- FONCTION SUPPRIMER UNE CATEGORIE ---------->
    #[Route("/categorie/{id}/delCategorie", name:"delCategorie_categorie")]
    public function delCategorie(ManagerRegistry $doctrine, Categorie $categorie)
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($categorie);
        $entityManager->flush();
        return $this->redirectToRoute('app_categorie');
    }
     
    //<---------- FONCTION AFFICHER CATEGORIE ---------->
    #[Route('/categorie/{id}', name: 'show_categorie')]
    public function show(Categorie $categorie): Response
    {       
        return $this->render('categorie/show.html.twig', [
           'categorie' => $categorie,
        ]);
    }
}

