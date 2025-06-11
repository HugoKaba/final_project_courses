<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241201000000 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add type column to notification table';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE notification ADD type VARCHAR(50) NOT NULL DEFAULT \'product\'');
    $this->addSql('ALTER TABLE notification ADD concerned_user_id INT DEFAULT NULL');
    $this->addSql('ALTER TABLE notification ADD INDEX IDX_BF5476CA8C03F15C (concerned_user_id)');
    $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA8C03F15C FOREIGN KEY (concerned_user_id) REFERENCES user (id)');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA8C03F15C');
    $this->addSql('ALTER TABLE notification DROP INDEX IDX_BF5476CA8C03F15C');
    $this->addSql('ALTER TABLE notification DROP concerned_user_id');
    $this->addSql('ALTER TABLE notification DROP type');
  }
}
