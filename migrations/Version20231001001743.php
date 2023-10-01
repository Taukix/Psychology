<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231001001743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE post_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE post (id INT NOT NULL, post_user_id INT NOT NULL, title VARCHAR(100) NOT NULL, description VARCHAR(200) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, state VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D9A8664A6 ON post (post_user_id)');
        $this->addSql('COMMENT ON COLUMN post.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D9A8664A6 FOREIGN KEY (post_user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE post_id_seq CASCADE');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8D9A8664A6');
        $this->addSql('DROP TABLE post');
    }
}
