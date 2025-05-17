<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

final class ProductController extends AbstractController
{
#[Route('/produits', name:'produit.index')]
public function listeProduits(Request $request, ProductRepository $repository, ImageRepository $imageRepo): Response
{
    $page = max(1, (int)$request->query->get('page', 1));
    $limit = 8;

    $result = $repository->findPaginatedProduits($page, $limit);
    $produits = $result['data'];
    $total = $result['total'];
    $pages = ceil($total / $limit);
    $images = $imageRepo->findAll();

    return $this->render('product/shop.html.twig', [
        'produits' => $produits,
        'images' => $images,
        'page' => $page,
        'pages' => $pages,
        'total' => $total,
        'limit' => $limit // 👈 Ajouter cette ligne
    ]);
}


}
