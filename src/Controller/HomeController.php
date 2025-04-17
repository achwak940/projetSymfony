<?php

namespace App\Controller;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $user->setFirstname("Lotfi")
            ->setLastname("melki")
            ->setAdress("Sousse")
            ->setEmail("Lotfiki@gmail.com")
            ->setRoles(['ROLE_ADMIN']) // Toujours mettre un rôle valide (ex: ROLE_USER)
            ->setTelephone(26362544);
        // Hasher le mot de passe correctement
        $hashedPassword = $hasher->hashPassword($user, '1234');
        $user->setPassword($hashedPassword);
        $em->persist($user);
        $em->flush();
        return $this->render('home/pageAcceuil.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    
    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
    return $this->render('home/contact.html.twig');
    }
    #[Route('/aboutus', name: 'about')]
    public function about(): Response
    {
    return $this->render('home/about.html.twig');
    }
    #[Route('/panier', name: 'panier')]
    public function panier(): Response
    {
    return $this->render('home/cart.html.twig');
    }
}

