<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class CartController extends AbstractController{
    #[Route('/cart', name: 'cart_show')]
    public function show(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        $cart = array_map(function ($item) {
            return array_merge($item, [
                'image' => $item['image'] ?? 'default.jpg', // Ajouter une image par défaut si manquante
            ]);
        }, $cart);

        $total = array_reduce($cart, fn($sum, $item) => $sum + $item['price'], 0);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'total' => $total,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add', methods: ['POST'])]
    public function add(Product $product, Request $request, SessionInterface $session): Response
    {
        // Récupérer la taille choisie
        $size = $request->request->get('size');
        
        if (!$size) {
            $this->addFlash('error', 'Veuillez sélectionner une taille.');
            return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
        }

        // Récupérer le panier depuis la session (ou en créer un nouveau)
        $cart = $session->get('cart', []);

        // Ajouter le produit et la taille au panier
        $cart[] = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'size' => $size,
            'image' => $product->getImage(),
        ];

        // Sauvegarder le panier dans la session
        $session->set('cart', $cart);

        $this->addFlash('success', 'Produit ajouté au panier avec succès.');

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/remove/{index}', name: 'cart_remove', methods: ['POST'])]
    public function remove(int $index, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
    
        if (isset($cart[$index])) {
            unset($cart[$index]);
            $cart = array_values($cart); // Réindexation
            $session->set('cart', $cart);
        }
    
        $this->addFlash('success', 'Article retiré du panier.');
    
        return $this->redirectToRoute('cart_show');
    }
}
