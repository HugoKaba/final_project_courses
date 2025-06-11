<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
  #[Route('/produits', name: 'app_produit_list')]
  public function list(ProduitRepository $produitRepository): Response
  {
    $produits = $produitRepository->findAll();

    return $this->render('produit/list.html.twig', [
      'produits' => $produits,
    ]);
  }

  #[Route('/produit/{id}', name: 'app_produit_show')]
  public function show(Produit $produit): Response
  {
    return $this->render('produit/show.html.twig', [
      'produit' => $produit,
    ]);
  }
}
