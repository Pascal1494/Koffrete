<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260715225136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_item (id INT AUTO_INCREMENT NOT NULL, `condition` VARCHAR(255) NOT NULL, personal_notes LONGTEXT DEFAULT NULL, acquired_at DATETIME NOT NULL, user_id INT NOT NULL, media_id INT NOT NULL, INDEX IDX_659A69D7A76ED395 (user_id), INDEX IDX_659A69D7EA9FDD75 (media_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user_item ADD CONSTRAINT FK_659A69D7A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_item ADD CONSTRAINT FK_659A69D7EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE loan DROP FOREIGN KEY `FK_C5D30D03EA9FDD75`');
        $this->addSql('DROP INDEX IDX_C5D30D03EA9FDD75 ON loan');
        $this->addSql('ALTER TABLE loan CHANGE media_id user_item_id INT NOT NULL');
        $this->addSql('ALTER TABLE loan ADD CONSTRAINT FK_C5D30D034A624C54 FOREIGN KEY (user_item_id) REFERENCES user_item (id)');
        $this->addSql('CREATE INDEX IDX_C5D30D034A624C54 ON loan (user_item_id)');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY `FK_6A2CA10CA76ED395`');
        $this->addSql('DROP INDEX IDX_6A2CA10CA76ED395 ON media');
        $this->addSql('ALTER TABLE media DROP user_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_item DROP FOREIGN KEY FK_659A69D7A76ED395');
        $this->addSql('ALTER TABLE user_item DROP FOREIGN KEY FK_659A69D7EA9FDD75');
        $this->addSql('DROP TABLE user_item');
        $this->addSql('ALTER TABLE loan DROP FOREIGN KEY FK_C5D30D034A624C54');
        $this->addSql('DROP INDEX IDX_C5D30D034A624C54 ON loan');
        $this->addSql('ALTER TABLE loan CHANGE user_item_id media_id INT NOT NULL');
        $this->addSql('ALTER TABLE loan ADD CONSTRAINT `FK_C5D30D03EA9FDD75` FOREIGN KEY (media_id) REFERENCES media (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_C5D30D03EA9FDD75 ON loan (media_id)');
        $this->addSql('ALTER TABLE media ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT `FK_6A2CA10CA76ED395` FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6A2CA10CA76ED395 ON media (user_id)');
    }
}
