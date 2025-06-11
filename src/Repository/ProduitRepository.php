<?php

namespace App\Repository;

use App\Entity\Produit;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Produit::class);
  }

  public function save(Produit $entity, bool $flush = false): void
  {
    $this->getEntityManager()->persist($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  public function remove(Produit $entity, bool $flush = false): void
  {
    $this->getEntityManager()->remove($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  public function findByCreatedBy(User $user): array
  {
    return $this->createQueryBuilder('p')
      ->andWhere('p.createdBy = :user')
      ->setParameter('user', $user)
      ->orderBy('p.createdAt', 'DESC')
      ->getQuery()
      ->getResult();
  }

  public function findOneByIdAndCreatedBy(int $id, User $user): ?Produit
  {
    return $this->createQueryBuilder('p')
      ->andWhere('p.id = :id')
      ->andWhere('p.createdBy = :user')
      ->setParameter('id', $id)
      ->setParameter('user', $user)
      ->getQuery()
      ->getOneOrNullResult();
  }
}
