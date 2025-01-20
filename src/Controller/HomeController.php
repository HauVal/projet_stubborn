<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController{
    #[Route('/', name: 'home')]
    public function index(ProductRepository $productRepository): Response
    {
        $highlightedProducts = $productRepository->findBy(['highlight' => true], null, 3);

        return $this->render('home/index.html.twig', [
            'products' => $highlightedProducts,
        ]);
    }
}
