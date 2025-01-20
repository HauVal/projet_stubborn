<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductStock;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        ProductRepository $productRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Liste de tous les produits
        $products = $productRepository->findAll();
    
        // Gestion du formulaire d'ajout
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Créez des stocks par défaut pour chaque taille
            foreach (['XS', 'S', 'M', 'L', 'XL'] as $size) {
                $stock = new ProductStock();
                $stock->setSize($size);
                $stock->setQuantity(0); // Stock initial
                $product->addStock($stock);
            }
    
            $entityManager->persist($product);
            $entityManager->flush();
    
            $this->addFlash('success', 'Produit ajouté avec succès.');
            return $this->redirectToRoute('admin_index');
        }
    
        return $this->render('admin/index.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/update/{id}', name: 'update', methods: ['POST'])]
    public function update(
        Product $product,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $data = $request->request->all();
    
        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setHighlight(isset($data['highlight']));
    
        foreach ($product->getStocks() as $stock) {
            if (isset($data['stock_' . $stock->getSize()])) {
                $stock->setQuantity((int) $data['stock_' . $stock->getSize()]);
            }
        }
    
        $entityManager->flush();
    
        $this->addFlash('success', 'Produit modifié avec succès.');
        return $this->redirectToRoute('admin_index');
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Product $product, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('success', 'Produit supprimé avec succès.');
        return $this->redirectToRoute('admin_index');
    }
}
