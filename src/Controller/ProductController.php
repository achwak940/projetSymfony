<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
final class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }
    #[Route('/produits', name:'produit.index')]
    public function listeProduits(Request $request,ProductRepository $repository,EntityManagerInterface $en) : Response
    {
        $produits= $repository->findAll();
        return $this->render('product/shop.html.twig',[
            'produits'=>
            $produits
        ]
    );
    }
}
