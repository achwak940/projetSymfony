<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DaschbordController extends AbstractController
{
    #[Route('/daschbord', name: 'app_daschbord')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser(); // Récupère l'utilisateur connecté
    return $this->render('daschbord/index.html.twig', [
        'controller_name' => 'DaschbordController',
        'user' => $user,  // Passe l'utilisateur à la vue
    ]);
    }
}
