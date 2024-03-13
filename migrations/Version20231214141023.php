<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231214141023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart_items (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, carts_id INT NOT NULL, price DOUBLE PRECISION DEFAULT NULL, quantity INT DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_BEF484454584665A (product_id), INDEX IDX_BEF48445BCB5C6F5 (carts_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carts (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, status VARCHAR(255) DEFAULT NULL, cteated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_4E004AACB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart_items ADD CONSTRAINT FK_BEF484454584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE cart_items ADD CONSTRAINT FK_BEF48445BCB5C6F5 FOREIGN KEY (carts_id) REFERENCES carts (id)');
        $this->addSql('ALTER TABLE carts ADD CONSTRAINT FK_4E004AACB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_items DROP FOREIGN KEY FK_BEF484454584665A');
        $this->addSql('ALTER TABLE cart_items DROP FOREIGN KEY FK_BEF48445BCB5C6F5');
        $this->addSql('ALTER TABLE carts DROP FOREIGN KEY FK_4E004AACB03A8386');
        $this->addSql('DROP TABLE cart_items');
        $this->addSql('DROP TABLE carts');
    }
}
