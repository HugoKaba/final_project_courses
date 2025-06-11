<?php

namespace App\Tests\Controller\Api;

use App\Controller\Api\MyProductsController;
use App\Entity\Produit;
use App\Entity\User;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Psr\Container\ContainerInterface;

class MyProductsControllerTest extends KernelTestCase
{
  private ContainerInterface $container;

  protected function setUp(): void
  {
    self::bootKernel();
    $this->container = static::getContainer();
  }

  public function testInvokeWithExistingProducts(): void
  {
    $user = $this->createMock(User::class);

    $produit1 = $this->createMock(Produit::class);
    $produit1->method('getId')->willReturn(1);
    $produit1->method('getNom')->willReturn('Produit 1');
    $produit1->method('getPrix')->willReturn(100);
    $produit1->method('getCategory')->willReturn('Catégorie A');
    $produit1->method('getDescription')->willReturn('Description 1');
    $produit1->method('getCreatedAt')->willReturn(new \DateTimeImmutable('2023-01-01 10:00:00'));
    $produit1->method('getUpdatedAt')->willReturn(new \DateTimeImmutable('2023-01-02 12:00:00'));

    $produit2 = $this->createMock(Produit::class);
    $produit2->method('getId')->willReturn(2);
    $produit2->method('getNom')->willReturn('Produit 2');
    $produit2->method('getPrix')->willReturn(200);
    $produit2->method('getCategory')->willReturn('Catégorie B');
    $produit2->method('getDescription')->willReturn('Description 2');
    $produit2->method('getCreatedAt')->willReturn(new \DateTimeImmutable('2023-02-01 10:00:00'));
    $produit2->method('getUpdatedAt')->willReturn(new \DateTimeImmutable('2023-02-02 12:00:00'));

    $produitRepository = $this->createMock(ProduitRepository::class);
    $produitRepository->method('findByCreatedBy')->with($user)->willReturn([$produit1, $produit2]);

    $controller = new class($user, $this->container) extends MyProductsController {
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

    $response = $controller->__invoke($produitRepository);

    $this->assertInstanceOf(JsonResponse::class, $response);
    $this->assertEquals(200, $response->getStatusCode());

    $data = json_decode($response->getContent(), true);
    $this->assertCount(2, $data);

    $this->assertEquals(1, $data[0]['id']);
    $this->assertEquals('Produit 1', $data[0]['nom']);
    $this->assertEquals(100, $data[0]['prix']);

    $this->assertEquals(2, $data[1]['id']);
    $this->assertEquals('Produit 2', $data[1]['nom']);
    $this->assertEquals(200, $data[1]['prix']);
  }

  public function testInvokeWithNoProducts(): void
  {
    $user = $this->createMock(User::class);

    $produitRepository = $this->createMock(ProduitRepository::class);
    $produitRepository->method('findByCreatedBy')->with($user)->willReturn([]);

    $controller = new class($user, $this->container) extends MyProductsController {
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

    $response = $controller->__invoke($produitRepository);

    $this->assertInstanceOf(JsonResponse::class, $response);
    $this->assertEquals(200, $response->getStatusCode());

    $data = json_decode($response->getContent(), true);
    $this->assertIsArray($data);
    $this->assertCount(0, $data);
  }
}
