<?php

namespace App\Controller;
use App\Entity\Cart;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Commande;
use App\Entity\DetailCommande;
use Symfony\Component\Mime\Email;
use App\Repository\CartRepository;
use App\Repository\UserRepository;
use App\Repository\ImageRepository;
use App\Repository\ProductRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DetailCommandeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
public function index(
    Request $request,
    EntityManagerInterface $em,
    UserPasswordHasherInterface $hasher,
    ProductRepository $productRepo,
    ImageRepository $imageRepo // Ajout du repository pour les images
): Response {
    $user = $this->getUser();
    // Récupérer les derniers produits avec leurs images
    $lastProducts = $productRepo->findLastThreeProducts();
    
    // Optionnellement, récupérer toutes les images si nécessaire (ici cela dépend de la logique du projet)
    // $allImages = $imageRepo->findAll(); // Si tu veux toutes les images, mais cela peut être inutile

    return $this->render('home/pageAcceuil.html.twig', [
        'controller_name' => 'HomeController',
        'user' => $user,
        'lastProducts' => $lastProducts // Passer les produits avec images à la vue
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
public function add(Product $product, Request $request, EntityManagerInterface $em, CartRepository $cartRepo): Response
{
    $user = $this->getUser();
    $quantity = $request->query->get('quantity', 1); // quantité à ajouter, par défaut 1

    // Vérifier que le stock est suffisant AVANT d'ajouter au panier
    if ($product->getStock() < $quantity) {
        $this->addFlash('error', 'Stock insuffisant pour ce produit.');
        return $this->redirectToRoute('cart_show');
    }

    $cartItem = $cartRepo->findOneBy([
        'user' => $user,
        'product' => $product
    ]);

    if ($cartItem) {
        // Ici, on pourrait aussi vérifier que la quantité totale dans le panier ne dépasse pas le stock
        $newQuantity = $cartItem->getQuantity() + $quantity;
        if ($newQuantity > $product->getStock()) {
            $this->addFlash('error', 'Stock insuffisant pour ce produit avec la quantité demandée.');
            return $this->redirectToRoute('cart_show');
        }
        $cartItem->setQuantity($newQuantity);
    } else {
        $cartItem = new Cart();
        $cartItem->setUser($user);
        $cartItem->setProduct($product);
        $cartItem->setQuantity($quantity);
        $em->persist($cartItem);
    }

    // **On ne diminue PAS le stock ici**

    $em->flush();

    return $this->redirectToRoute('cart_show');
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
        $quantity = $cartItem->getQuantity();

        // Rendre la quantité au stock
        $product->setStock($product->getStock() + $quantity);

        $em->remove($cartItem);
        $em->flush();
    }

    return $this->redirectToRoute('cart_show');
}

#[Route('/valider-commande', name: 'valider_commande')]
public function validerCommande(
    EntityManagerInterface $em,
    CartRepository $cartRepo
): Response {
    $user = $this->getUser();
    $cartItems = $cartRepo->findBy(['user' => $user]);

    if (empty($cartItems)) {
        $this->addFlash('error', 'Votre panier est vide.');
        return $this->redirectToRoute('cart_afficher');
    }

    // 1. Vérifier le stock pour chaque produit
    foreach ($cartItems as $item) {
        $produit = $item->getProduct();
        $quantite = $item->getQuantity();

        if ($produit->getStock() < $quantite) {
            $this->addFlash('error', "Stock insuffisant pour le produit : " . $produit->getName());
            return $this->redirectToRoute('cart_afficher');
        }
    }

    // 2. Créer la commande
    $commande = new Commande();
    $commande->setNo($user);
    $commande->setCreatedAt(new \DateTimeImmutable());
    $commande->setStatut('En attente');
    $montantTotal = 0;

    // 3. Créer les détails de la commande et diminuer le stock
    foreach ($cartItems as $item) {
        $produit = $item->getProduct();
        $quantite = $item->getQuantity();
        $prix = $produit->getPrix();

        // Diminuer le stock
        $produit->setStock($produit->getStock() - $quantite);

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

    // 4. Vider le panier
    foreach ($cartItems as $item) {
        $em->remove($item);
    }

    $em->flush();

    $this->addFlash('success', 'Commande validée avec succès !');
    return $this->redirectToRoute('confirmation');
}


#[Route('/email/{id}', name: 'email')]
public function email($id, UserRepository $userRepo, MailerInterface $mailer, CommandeRepository $cmd): Response
{
    $user = $userRepo->find($id);

    if ($user === null) {
        throw $this->createNotFoundException('Utilisateur non trouvé');
    }

    $firstname = htmlspecialchars($user->getFirstname(), ENT_QUOTES, 'UTF-8');

    $htmlContent = <<<HTML
    <div style="font-family: Arial, sans-serif; padding: 20px; background-color: #f8f9fa; color: #333;">
        <h2 style="color: #007bff;">Bonjour $firstname,</h2>
        <p>Votre commande a été <strong>validée avec succès</strong> !</p>
        <p>Merci pour votre confiance et votre achat.</p>
        <div style="margin-top: 20px; padding: 15px; background-color: #e9ecef; border-radius: 5px;">
            <p><strong>Numéro du livreur :</strong> <span style="font-size: 18px; color: #28a745;">93164748</span></p>
        </div>
        <p style="margin-top: 30px;">Cordialement,<br>L’équipe de livraison</p>
    </div>
    HTML;

    $email = (new Email())
        ->from('rahmabassou3@gmail.com')
        ->to($user->getEmail())
        ->subject('Confirmation de votre commande')
        ->html($htmlContent);

    try {
        $mailer->send($email);
        $this->addFlash('success', 'Email envoyé avec succès.');
    } catch (\Exception $e) {
        $this->addFlash('error', 'L\'envoi de l\'email a échoué : ' . $e->getMessage());
    }

    return $this->render('home/thankyou.html.twig');
}


#[Route('/commande/details', name: 'commande_details')]
public function showDetails(
    Request $request,
    CommandeRepository $commandeRepo,
    DetailCommandeRepository $detailRepo
): Response {
    $page = max(1, $request->query->getInt('page', 1));
    $limit = 3;

    $pagination = $commandeRepo->findPaginatedCommandes($page, $limit);

    if (!$pagination['data']) {
        throw $this->createNotFoundException('Aucune commande trouvée.');
    }

    $commandesAvecDetails = [];
    foreach ($pagination['data'] as $commande) {
        $details = $detailRepo->findBy(['commande' => $commande]);
        $commandesAvecDetails[] = [
            'commande' => $commande,
            'details' => $details,
        ];
    }

    return $this->render('dashboard/details.html.twig', [
        'commandesAvecDetails' => $commandesAvecDetails,
        'total' => $pagination['total'],
        'page' => $page,
        'limit' => $limit,
    ]);
}


}

