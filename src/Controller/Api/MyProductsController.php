<?php

namespace App\Controller\Api;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class MyProductsController extends AbstractController
{
  public function __invoke(ProduitRepository $produitRepository): JsonResponse
  {
    $user = $this->getUser();
    $produits = $produitRepository->findByCreatedBy($user);

    $data = [];
    foreach ($produits as $produit) {
      $data[] = [
        'id' => $produit->getId(),
        'nom' => $produit->getNom(),
        'prix' => $produit->getPrix(),
        'category' => $produit->getCategory(),
        'description' => $produit->getDescription(),
        'createdAt' => $produit->getCreatedAt()->format('Y-m-d H:i:s'),
        'updatedAt' => $produit->getUpdatedAt()->format('Y-m-d H:i:s'),
      ];
    }

    return $this->json($data);
  }
}
