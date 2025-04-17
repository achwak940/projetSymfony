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

