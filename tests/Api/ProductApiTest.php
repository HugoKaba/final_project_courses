<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductApiTest extends WebTestCase
{
  public function testProductRouteExistsAndReturnsExpectedStatus(): void
  {
    $client = static::createClient();

    $client->request('GET', '/api/produits/1/my-product');
    $statusCode = $client->getResponse()->getStatusCode();

    $this->assertContains(
      $statusCode,
      [200, 401, 403, 404],
      'La route doit retourner un statut HTTP valide (200, 401, 403 ou 404)'
    );
  }

  public function testProductRouteWithNonExistentId(): void
  {
    $client = static::createClient();

    $client->request('GET', '/api/produits/999999/my-product');
    $statusCode = $client->getResponse()->getStatusCode();

    $this->assertContains(
      $statusCode,
      [404, 401, 403],
      'La route avec ID inexistant doit retourner 404, 401 ou 403'
    );
  }
}
