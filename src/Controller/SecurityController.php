<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
  #[Route(path: '/login', name: 'app_login')]
  public function login(AuthenticationUtils $authenticationUtils): Response
  {
    if ($this->getUser()) {
      return $this->redirectToRoute('app_produit_list');
    }

    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('security/login.html.twig', [
      'last_username' => $lastUsername,
      'error' => $error
    ]);
  }

  #[Route(path: '/logout', name: 'app_logout')]
  public function logout(): void
  {
    throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
  }

  #[Route(path: '/register', name: 'app_register')]
  public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
  {
    if ($this->getUser()) {
      return $this->redirectToRoute('app_produit_list');
    }

    if ($request->isMethod('POST')) {
      $user = new User();
      $user->setEmail($request->request->get('email'));
      $user->setNom($request->request->get('nom'));
      $user->setPrenom($request->request->get('prenom'));
      $user->setPoints(1000);

      $user->setPassword(
        $userPasswordHasher->hashPassword(
          $user,
          $request->request->get('password')
        )
      );

      $entityManager->persist($user);
      $entityManager->flush();

      $this->addFlash('success', 'Compte créé avec succès !');
      return $this->redirectToRoute('app_login');
    }

    return $this->render('security/register.html.twig');
  }
}
