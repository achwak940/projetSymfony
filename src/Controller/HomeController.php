<?php

namespace App\Controller;
use App\Entity\Cart;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Commande;
use App\Entity\DetailCommande;
use App\Repository\CartRepository;
use App\Repository\ImageRepository;
use App\Repository\ProductRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DetailCommandeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $user = $this->getUser(); 
        return $this->render('home/pageAcceuil.html.twig', [
            'controller_name' => 'HomeController',
            'user'=>$user
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
    #[Route('/add/{id}', name: 'cart_add')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(Product $product, EntityManagerInterface $em, CartRepository $cartRepo): Response
    {
        $user = $this->getUser();

        $cartItem = $cartRepo->findOneBy([
            'user' => $user,
            'product' => $product
        ]);

        if ($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
        } else {
            $cartItem = new Cart();
            $cartItem->setUser($user);
            $cartItem->setProduct($product);
            $cartItem->setQuantity(1);
            $em->persist($cartItem);
        }

        $em->flush();

        return $this->redirectToRoute('produit.index');
    }
    #[Route('/show', name: 'cart_show')]
public function show(CartRepository $cartRepo): Response
{
    $user = $this->getUser();
    $cartItems = $cartRepo->findBy(['user' => $user]);

    $products = [];
    $total = 0;

    // Récupérer les produits du panier
    foreach ($cartItems as $item) {
        $product = $item->getProduct();
        $quantity = $item->getQuantity();
        $totalProduct = $product->getPrix() * $quantity;

        $products[] = [
            'product' => $product,
            'quantity' => $quantity,
            'total' => $totalProduct
        ];

        $total += $totalProduct; // Calculer le total du panier
    }

    return $this->render('home/cart.html.twig', [
        'products' => $products,
        'total' => $total // Passer le total à la vue
    ]);
}
    #[Route('/remove/{id}', name: 'cart_remove')]
public function remove(Product $product, CartRepository $cartRepo, EntityManagerInterface $em): Response
{
    $user = $this->getUser();
    $cartItem = $cartRepo->findOneBy([
        'user' => $user,
        'product' => $product
    ]);

    if ($cartItem) {
        $em->remove($cartItem);
        $em->flush();
    }

    return $this->redirectToRoute('cart_show');
}
#[Route('/valider-commande', name: 'valider_commande')]
public function validerCommande(
    EntityManagerInterface $em,
    CartRepository $cartRepo,
    ProductRepository $productRepo
): Response {
    $user = $this->getUser();
    $cartItems = $cartRepo->findBy(['user' => $user]);

    if (empty($cartItems)) {
        $this->addFlash('error', 'Votre panier est vide.');
        return $this->redirectToRoute('cart_afficher');
    }

    // 1. Créer la commande
    $commande = new Commande();
    $commande->setNo($user);
    $commande->setCreatedAt(new \DateTimeImmutable());
    $commande->setStatut('En attente');
    $montantTotal = 0;
    // 2. Créer les détails de la commande
    foreach ($cartItems as $item) {
        $produit = $item->getProduct();
        $quantite = $item->getQuantity();
        $prix = $produit->getPrix();

        $detail = new DetailCommande();
        $detail->setProduct($produit);
        $detail->setCommande($commande);
        $detail->setQuantite($quantite);
        $detail->setPrixUnitaire($prix);

        $em->persist($detail);

        $montantTotal += $prix * $quantite;
    }

    $commande->setMontantTotal($montantTotal);
    $em->persist($commande);

    // 3. Vider le panier
    foreach ($cartItems as $item) {
        $em->remove($item);
    }

    $em->flush();

    $this->addFlash('success', 'Commande validée avec succès !');
    return $this->redirectToRoute('confirmation');
}
#[Route('/commande/details', name: 'commande_details')]
public function showDetails(
    CommandeRepository $commandeRepo,
    DetailCommandeRepository $detailRepo
): Response {
    $user = $this->getUser();

    // Récupérer la dernière commande de l'utilisateur (tu peux adapter selon ton besoin)
    $commande = $commandeRepo->findOneBy(
        ['no' => $user],
        ['created_at' => 'DESC']
    );

    if (!$commande) {
        throw $this->createNotFoundException('Aucune commande trouvée.');
    }

    $details = $detailRepo->findBy(['commande' => $commande]);

    return $this->render('dashboard/details.html.twig', [
        'commande' => $commande,
        'details' => $details
    ]);
}


}

