<?php

namespace App\EventListener;

use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
class TimestampListener
{
  public function prePersist(PrePersistEventArgs $event): void
  {
    $entity = $event->getObject();

    if (method_exists($entity, 'setCreatedAt')) {
      $entity->setCreatedAt(new \DateTimeImmutable());
    }

    if (method_exists($entity, 'setUpdatedAt')) {
      $entity->setUpdatedAt(new \DateTimeImmutable());
    }
  }

  public function preUpdate(PreUpdateEventArgs $event): void
  {
    $entity = $event->getObject();

    if (method_exists($entity, 'setUpdatedAt')) {
      $entity->setUpdatedAt(new \DateTimeImmutable());
    }
  }
}
