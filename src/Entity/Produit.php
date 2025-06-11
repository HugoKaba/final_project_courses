<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\ProduitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ApiResource(
  normalizationContext: ['groups' => ['produit:read']],
  denormalizationContext: ['groups' => ['produit:write']],
  operations: [
    new GetCollection(
      uriTemplate: '/produits/my-products',
      controller: 'App\Controller\Api\MyProductsController',
      security: 'is_granted("ROLE_ADMIN")',
      openapiContext: [
        'summary' => 'Récupère les produits créés par l\'utilisateur connecté',
        'description' => 'Route disponible uniquement pour les admins'
      ]
    ),
    new Get(
      uriTemplate: '/produits/{id}/my-product',
      controller: 'App\Controller\Api\MyProductController',
      security: 'is_granted("ROLE_ADMIN")',
      openapiContext: [
        'summary' => 'Récupère un produit créé par l\'utilisateur connecté',
        'description' => 'Route disponible uniquement pour les admins'
      ]
    )
  ]
)]
#[ApiFilter(SearchFilter::class, properties: ['category' => 'exact', 'nom' => 'partial'])]
class Produit
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[Groups(['produit:read'])]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[Groups(['produit:read', 'produit:write'])]
  private ?string $nom = null;

  #[ORM\Column]
  #[Groups(['produit:read', 'produit:write'])]
  private ?int $prix = null;

  #[ORM\Column(length: 255)]
  #[Groups(['produit:read', 'produit:write'])]
  private ?string $category = null;

  #[ORM\Column(type: Types::TEXT, nullable: true)]
  #[Groups(['produit:read', 'produit:write'])]
  private ?string $description = null;

  #[ORM\Column]
  #[Groups(['produit:read'])]
  private ?\DateTimeImmutable $createdAt = null;

  #[ORM\Column]
  #[Groups(['produit:read'])]
  private ?\DateTimeImmutable $updatedAt = null;

  #[ORM\ManyToOne(inversedBy: 'produits')]
  #[Groups(['produit:read'])]
  private ?User $createdBy = null;

  public function __construct()
  {
    // Les timestamps seront gérés par TimestampListener
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getNom(): ?string
  {
    return $this->nom;
  }

  public function setNom(string $nom): self
  {
    $this->nom = $nom;

    return $this;
  }

  public function getPrix(): ?int
  {
    return $this->prix;
  }

  public function setPrix(int $prix): self
  {
    $this->prix = $prix;

    return $this;
  }

  public function getCategory(): ?string
  {
    return $this->category;
  }

  public function setCategory(string $category): self
  {
    $this->category = $category;

    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(?string $description): self
  {
    $this->description = $description;

    return $this;
  }

  public function getCreatedAt(): ?\DateTimeImmutable
  {
    return $this->createdAt;
  }

  public function setCreatedAt(\DateTimeImmutable $createdAt): self
  {
    $this->createdAt = $createdAt;

    return $this;
  }

  public function getUpdatedAt(): ?\DateTimeImmutable
  {
    return $this->updatedAt;
  }

  public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
  {
    $this->updatedAt = $updatedAt;

    return $this;
  }

  public function getCreatedBy(): ?User
  {
    return $this->createdBy;
  }

  public function setCreatedBy(?User $createdBy): self
  {
    $this->createdBy = $createdBy;

    return $this;
  }
}
