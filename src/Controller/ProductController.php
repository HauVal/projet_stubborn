<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController{
    #[Route('/products', name: 'product_list')]
    public function list(ProductRepository $productRepository, Request $request)
    {
        // Récupération de la fourchette de prix depuis la requête
        $priceRange = $request->query->get('price_range');

        // Filtrage selon la fourchette de prix
        $products = $productRepository->findByPriceRange($priceRange);

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'priceRange' => $priceRange,
        ]);
    }

    #[Route('/product/{id}', name: 'product_show')]
    public function show(int $id, ProductRepository $productRepository): Response
    {
        // Récupérer le produit avec les tailles associées
        $product = $productRepository->find($id);
    
        if (!$product) {
            throw $this->createNotFoundException('Le produit demandé n\'existe pas.');
        }
    
        // Extraire les stocks pour les tailles
        $stocks = $product->getStocks();
    
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'stocks' => $stocks,
        ]);
    }

}
