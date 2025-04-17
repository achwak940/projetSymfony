<?php

namespace App\Controller;
use App\Entity\User;
use App\Repository\ImageRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $user->setFirstname("Hanin")
            ->setLastname("Bellili")
            ->setAdress("Sousse")
            ->setEmail("haninbellili2@gmail.com")
            ->setRoles(['ROLE_CLIENT']) // Toujours mettre un rôle valide (ex: ROLE_USER)
            ->setTelephone(28170520);
        // Hasher le mot de passe correctement
        $hashedPassword = $hasher->hashPassword($user, '0000');
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
    #[Route('/confirme', name: 'confirmation')]
    public function thankyou(): Response
    {
    return $this->render('home/thankyou.html.twig');
    }
    #[Route('/panier', name:'panier')]
    public function panier(Request $request,ProductRepository $repository,ImageRepository $image,EntityManagerInterface $en) : Response
    {
        $produits= $repository->findAll();
        $images=$image->findALl();
        return $this->render('home/cart.html.twig',[
            'produits'=>
            $produits,
            'images'=>$images
        ]
    );
    }
}

