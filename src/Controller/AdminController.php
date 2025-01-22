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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        ProductRepository $productRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $products = $productRepository->findAll();
    
        // Gestion du formulaire d'ajout
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            foreach (['XS', 'S', 'M', 'L', 'XL'] as $size) {
                $stock = new ProductStock();
                $stock->setSize($size);
                $quantity = $formData['stock_' . $size] ?? 0;
                $stock->setQuantity((int)$quantity);
                $product->addStock($stock);
            }

             // Gestion de l'upload de l'image
        /** @var UploadedFile $imageFile */
        $imageFile = $form->get('image')->getData();

        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('product_images_directory'),
                    $newFilename
                );
                $product->setImage($newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image.');
                return $this->redirectToRoute('admin_index');
            }
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

        $imageFile = $request->files->get('image'); // Récupère le fichier "image"

        if ($imageFile) {
            // Supprimez l'ancienne image si elle existe
            $oldImagePath = $this->getParameter('product_images_directory') . '/' . $product->getImage();
            if ($product->getImage() && file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        
            // Génération d'un nom unique pour la nouvelle image
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();
        
            try {
                // Déplacement de l'image dans le dossier public/imagesProduits
                $imageFile->move(
                    $this->getParameter('product_images_directory'),
                    $newFilename
                );
        
                // Mise à jour du champ "image" de l'entité
                $product->setImage($newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'enregistrement de l\'image.');
                return $this->redirectToRoute('admin_index');
            }
        }
    
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
    public function delete(
        Product $product,
        Request $request,
        EntityManagerInterface $entityManager,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        $submittedToken = $request->request->get('_token');

        // Vérifiez le token CSRF
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('delete' . $product->getId(), $submittedToken))) {
            $this->addFlash('error', 'Action non autorisée.');
            return $this->redirectToRoute('admin_index');
        }

        // Récupérez le chemin absolu de l'image associée au produit
        $imagePath = $this->getParameter('product_images_directory') . '/' . $product->getImage();
    
        // Vérifiez si le fichier existe, puis supprimez-le
        if ($product->getImage() && file_exists($imagePath)) {
            unlink($imagePath); // Supprime le fichier
        }

        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('success', 'Produit supprimé avec succès.');
        return $this->redirectToRoute('admin_index');
    }
}
