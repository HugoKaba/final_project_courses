<?php

namespace App\Tests\Controller\Api;

use App\Controller\Api\MyProductController;
use App\Entity\Produit;
use App\Entity\User;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Psr\Container\ContainerInterface;

class MyProductControllerTest extends KernelTestCase
{
  private ContainerInterface $container;

  protected function setUp(): void
  {
    self::bootKernel();
    $this->container = static::getContainer();
  }

  public function testInvokeWithExistingProduct(): void
  {
    $user = $this->createMock(User::class);

    $produit = $this->createMock(Produit::class);
    $produit->method('getId')->willReturn(1);
    $produit->method('getNom')->willReturn('Produit Test');
    $produit->method('getPrix')->willReturn(100);
    $produit->method('getCategory')->willReturn('Catégorie Test');
    $produit->method('getDescription')->willReturn('Description du produit');
    $produit->method('getCreatedAt')->willReturn(new \DateTimeImmutable('2023-01-01 10:00:00'));
    $produit->method('getUpdatedAt')->willReturn(new \DateTimeImmutable('2023-01-02 12:00:00'));

    $produitRepository = $this->createMock(ProduitRepository::class);
    $produitRepository->method('findOneByIdAndCreatedBy')->with(1, $user)->willReturn($produit);

    $controller = new class($user, $this->container) extends MyProductController {
      private UserInterface $mockUser;
      protected ContainerInterface $container;

      public function __construct(UserInterface $user, ContainerInterface $container)
      {
        $this->mockUser = $user;
        $this->container = $container;
        $this->setContainer($container);
      }

      public function getUser(): ?UserInterface
      {
        return $this->mockUser;
      }
    };

    $response = $controller->__invoke(1, $produitRepository);

    $this->assertInstanceOf(JsonResponse::class, $response);
    $this->assertEquals(200, $response->getStatusCode());

    $data = json_decode($response->getContent(), true);

    $this->assertEquals(1, $data['id']);
    $this->assertEquals('Produit Test', $data['nom']);
    $this->assertEquals(100, $data['prix']);
  }

  public function testInvokeWithNonExistingProduct(): void
  {
    $user = $this->createMock(User::class);

    $produitRepository = $this->createMock(ProduitRepository::class);
    $produitRepository->method('findOneByIdAndCreatedBy')->with(999, $user)->willReturn(null);

    $controller = new class($user, $this->container) extends MyProductController {
      private UserInterface $mockUser;
      protected ContainerInterface $container;

      public function __construct(UserInterface $user, ContainerInterface $container)
      {
        $this->mockUser = $user;
        $this->container = $container;
        $this->setContainer($container);
      }

      public function getUser(): ?UserInterface
      {
        return $this->mockUser;
      }
    };

    $response = $controller->__invoke(999, $produitRepository);

    $this->assertInstanceOf(JsonResponse::class, $response);
    $this->assertEquals(404, $response->getStatusCode());

    $data = json_decode($response->getContent(), true);
    $this->assertEquals('Produit non trouvé', $data['error']);
  }
}
