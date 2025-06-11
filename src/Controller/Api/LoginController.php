<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\User;

class LoginController extends AbstractController
{
  #[Route('/api/login_check', name: 'api_login', methods: ['POST'])]
  public function index(#[CurrentUser] ?User $user): JsonResponse
  {
    if (null === $user) {
      return $this->json([
        'message' => 'missing credentials',
      ], JsonResponse::HTTP_UNAUTHORIZED);
    }

    $token = '';

    return $this->json([
      'user'  => $user->getUserIdentifier(),
      'token' => $token,
    ]);
  }
}
