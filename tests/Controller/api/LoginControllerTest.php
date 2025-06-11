<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Controller\Api\LoginController;
use App\Entity\User;

class LoginControllerTest extends WebTestCase
{
  public function testLoginWithAuthenticatedUser(): void
  {
    self::bootKernel();

    $container = static::getContainer();
    $controller = $container->get(LoginController::class);

    $userMock = $this->createMock(User::class);
    $userMock->method('getUserIdentifier')->willReturn('mockuser@example.com');

    $response = $controller->index($userMock);

    $this->assertEquals(200, $response->getStatusCode());

    $data = json_decode($response->getContent(), true);
    $this->assertEquals('mockuser@example.com', $data['user']);
    $this->assertArrayHasKey('token', $data);
  }

  public function testLoginWithNullUser(): void
  {
    self::bootKernel();

    $container = static::getContainer();
    $controller = $container->get(LoginController::class);

    $response = $controller->index(null);

    $this->assertEquals(401, $response->getStatusCode());

    $data = json_decode($response->getContent(), true);
    $this->assertEquals('missing credentials', $data['message']);
  }
}
