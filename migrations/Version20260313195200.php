<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260313195200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(50) NOT NULL, subject VARCHAR(50) NOT NULL, message LONGTEXT NOT NULL, date_send DATE NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE opinion (id INT AUTO_INCREMENT NOT NULL, note NUMERIC(2, 1) NOT NULL, comment LONGTEXT NOT NULL, date_send DATE NOT NULL, reservation_id INT NOT NULL, UNIQUE INDEX UNIQ_AB02B027B83297E7 (reservation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, total NUMERIC(10, 2) NOT NULL, date_payment DATETIME NOT NULL, mode VARCHAR(50) NOT NULL, status VARCHAR(50) NOT NULL, reservation_id INT NOT NULL, UNIQUE INDEX UNIQ_6D28840DB83297E7 (reservation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) DEFAULT NULL, room_id INT DEFAULT NULL, service_id INT DEFAULT NULL, INDEX IDX_14B7841854177093 (room_id), INDEX IDX_14B78418ED5CA9E6 (service_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, date_return DATETIME NOT NULL, date_arrived DATETIME NOT NULL, total_price NUMERIC(10, 2) NOT NULL, status VARCHAR(50) NOT NULL, user_id INT NOT NULL, room_id INT NOT NULL, INDEX IDX_42C84955A76ED395 (user_id), INDEX IDX_42C8495554177093 (room_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, description LONGTEXT NOT NULL, price_night NUMERIC(10, 2) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, description LONGTEXT NOT NULL, price NUMERIC(10, 2) NOT NULL, time NUMERIC(4, 2) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE opinion ADD CONSTRAINT FK_AB02B027B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B7841854177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495554177093 FOREIGN KEY (room_id) REFERENCES room (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE opinion DROP FOREIGN KEY FK_AB02B027B83297E7');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DB83297E7');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B7841854177093');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418ED5CA9E6');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495554177093');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE opinion');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
