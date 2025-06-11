<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
class ProfileController extends AbstractController
{
  #[Route('/', name: 'app_profile_show')]
  #[IsGranted('ROLE_USER')]
  public function show(): Response
  {
    return $this->render('profile/show.html.twig');
  }

  #[Route('/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
  #[IsGranted('ROLE_USER')]
  public function edit(Request $request, EntityManagerInterface $entityManager): Response
  {
    /** @var User $user */
    $user = $this->getUser();

    if ($request->isMethod('POST')) {
      $nom = $request->request->get('nom');
      $prenom = $request->request->get('prenom');

      if ($nom && $prenom) {
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $entityManager->flush();

        $this->addFlash('success', 'Profil mis à jour avec succès !');
        return $this->redirectToRoute('app_profile_show');
      }
    }

    return $this->render('profile/edit.html.twig', [
      'user' => $user,
    ]);
  }
}
