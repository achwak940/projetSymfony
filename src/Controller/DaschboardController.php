<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\UserRepository;
use App\Repository\ImageRepository;
use App\Repository\ProductRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class DaschboardController extends AbstractController
{
    #[Route('/daschboard', name: 'app_daschboard')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser(); // Récupère l'utilisateur connecté
    return $this->render('daschboard/index.html.twig', [
        'controller_name' => 'DaschboardController',
        'user' => $user,  // Passe l'utilisateur à la vue
    ]);
    }
    #[Route('/admin', name: 'admin')]
    public function admin(
        Request $request,
        ProductRepository $repository,
        ImageRepository $image,
        EntityManagerInterface $en,
        UserRepository $user,
        CommandeRepository $cmd
    ): Response
    {
        $page = $request->query->getInt('page', 1); // récupère le n° de page depuis l'URL (ex: ?page=2)
        $limit = 10; // nombre de produits par page
    
        // Récupération paginée des produits
        $pagination = $repository->findPaginatedProduits($page, $limit);
        $images = $image->findAll();
        $productCount = $repository->count([]);
        $userCount = $user->count([]);
        $cmdCount = $cmd->count([]);
    
        return $this->render('dashboard/admin.html.twig', [
            'produits' => $pagination['data'],      // produits paginés
            'images' => $images,
            'nbr_produit' => $productCount,
            'nbr_user' => $userCount,
            'nbr_cmd' => $cmdCount,
            'current_page' => $page,
            'total_pages' => ceil($pagination['total'] / $limit),
        ]);
    }
    
    #[Route('/create', name:'ajouter')]
    public function create(Request $request,EntityManagerInterface $en, SluggerInterface $slugger): Response
    {
        
        $product=new Product();
        $form=$this->createForm(ProductType::class,$product);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){
            $product->setCreatedAt(new \DateTimeImmutable());
            $slug =$slugger->slug($product->getName());
            $product->setSlug($slug);
            $imageName = $form->get('images')->getData();
            if ($imageName) {
                $image = new Image();
                $image->setName($imageName);
                $image->setProduct($product); // Lier au produit
                $en->persist($image);
            }    
            $en->persist($product);
            $en->flush();
            $this->addFlash('success', 'Le produit a été ajouté avec succès');
            return $this->redirectToRoute('admin');
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Veuillez remplir tous les champs correctement.');
        }
        return $this->render('dashboard/create.html.twig', [
            'form'=>$form
        ]);
    }
    #[Route('/delete/{id}', name:'delete',methods:['DELETE'])]
    public function delete(Product $product,EntityManagerInterface $en): Response
    {
        // Supprimer d'abord les détails de commande liés
    foreach ($product->getDetailsCommande() as $detail) {
        $en->remove($detail);
    }
        foreach ($product->getImage() as $img) {
            $en->remove($img);
        }
        $en->remove($product);
        $en->flush();
        $this->addFlash('success', 'le produit a été supprimée');
        return $this->redirectToRoute('admin');
    }
    #[Route('/affiche', name:'affiche')]
    public function client(Request $request,UserRepository $repository,EntityManagerInterface $en) : Response
    {
        $clients= $repository->findAll();
        return $this->render('dashboard/client.html.twig',[
            'clients'=>
            $clients,
        ]
    );
    }
    #[Route('/edit/{id}', name:'edit')]
    public function edit(Product $product,Request $request,EntityManagerInterface $en, SluggerInterface $slugger): Response
    {
        
        $form=$this->createForm(ProductType::class,$product);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){
            $en->persist($product);
            $en->flush();
            $this->addFlash('success', 'Le produit a été modifié avec succès');
            return $this->redirectToRoute('admin');
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Veuillez remplir tous les champs correctement.');
        }
        return $this->render('dashboard/edit.html.twig', [
            'form'=>$form
        ]);
    }
}
