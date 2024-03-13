<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231214112645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A12469DE2');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7B03A8386');
        $this->addSql('ALTER TABLE cart_items DROP FOREIGN KEY FK_BEF48445DE18E50B');
        $this->addSql('ALTER TABLE cart_items DROP FOREIGN KEY FK_BEF4844520AEF35F');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993989D86650F');
        $this->addSql('ALTER TABLE order_lines DROP FOREIGN KEY FK_CC9FF86BFCDAEAAA');
        $this->addSql('ALTER TABLE order_lines DROP FOREIGN KEY FK_CC9FF86B4584665A');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0F9D86650F');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE cart_items');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_lines');
        $this->addSql('DROP TABLE reviews');
        $this->addSql('DROP INDEX IDX_B3BA5A5A12469DE2 ON products');
        $this->addSql('DROP INDEX UNIQ_B3BA5A5A989D9B62 ON products');
        $this->addSql('ALTER TABLE products ADD image VARCHAR(255) DEFAULT NULL, DROP category_id, DROP image_thumbnail, DROP size, DROP quantity, DROP discount_type, DROP discount_value, CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE summary summary VARCHAR(255) DEFAULT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE price price DOUBLE PRECISION DEFAULT NULL, CHANGE on_sale on_sale TINYINT(1) DEFAULT NULL, CHANGE status status TINYINT(1) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_BA388B7B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE cart_items (id INT AUTO_INCREMENT NOT NULL, product_id_id INT NOT NULL, cart_id_id INT NOT NULL, price DOUBLE PRECISION DEFAULT NULL, quantity INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_BEF48445DE18E50B (product_id_id), INDEX IDX_BEF4844520AEF35F (cart_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, slug LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, name LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, tags LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F52993989D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE order_lines (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, order_id_id INT NOT NULL, price DOUBLE PRECISION DEFAULT NULL, quantity INT DEFAULT NULL, INDEX IDX_CC9FF86BFCDAEAAA (order_id_id), INDEX IDX_CC9FF86B4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reviews (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, rating DOUBLE PRECISION DEFAULT NULL, comment LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6970EB0F9D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cart_items ADD CONSTRAINT FK_BEF48445DE18E50B FOREIGN KEY (product_id_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE cart_items ADD CONSTRAINT FK_BEF4844520AEF35F FOREIGN KEY (cart_id_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993989D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE order_lines ADD CONSTRAINT FK_CC9FF86BFCDAEAAA FOREIGN KEY (order_id_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_lines ADD CONSTRAINT FK_CC9FF86B4584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE products ADD category_id INT NOT NULL, ADD image_thumbnail VARCHAR(255) NOT NULL, ADD quantity INT NOT NULL, ADD discount_type VARCHAR(255) DEFAULT NULL, ADD discount_value DOUBLE PRECISION DEFAULT NULL, CHANGE title title VARCHAR(255) NOT NULL, CHANGE summary summary LONGTEXT NOT NULL, CHANGE description description LONGTEXT NOT NULL, CHANGE price price DOUBLE PRECISION NOT NULL, CHANGE on_sale on_sale TINYINT(1) NOT NULL, CHANGE status status TINYINT(1) NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL, CHANGE image size VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A12469DE2 ON products (category_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B3BA5A5A989D9B62 ON products (slug)');
    }
}
