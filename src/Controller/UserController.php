<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/utilisateur', name: 'app_utilisateur')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $utilisateur  = $doctrine->getRepository(Utilisateur::class)->findBy([],["pseudo"=>"ASC"]);
        return $this->render('utilisateur/index.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }
}
