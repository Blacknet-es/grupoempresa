<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210524135838 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE app_user (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cuenta_bancaria (id INT AUTO_INCREMENT NOT NULL, socio_id INT NOT NULL, iban VARCHAR(4) NOT NULL, entidad VARCHAR(255) NOT NULL, oficina VARCHAR(255) NOT NULL, dc VARCHAR(255) NOT NULL, num_cuenta VARCHAR(255) NOT NULL, activa TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_ECD0C9CEDA04E6A9 (socio_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, url LONGTEXT NOT NULL, token VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, photo_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, mobile_phone VARCHAR(255) DEFAULT NULL, user_type VARCHAR(255) DEFAULT NULL, locale VARCHAR(10) DEFAULT \'es\', _user_type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_957A6479C05FB297 (confirmation_token), INDEX IDX_957A64797E9E4C8C (photo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gallery (id INT AUTO_INCREMENT NOT NULL, gallery LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, image VARCHAR(255) DEFAULT NULL, original_image VARCHAR(255) DEFAULT NULL, upload_dir VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metodo_pago (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, clave VARCHAR(255) NOT NULL, periodo INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE municipios (id INT AUTO_INCREMENT NOT NULL, id_provincia INT DEFAULT NULL, municipio VARCHAR(255) NOT NULL, INDEX IDX_BBFAB58653AF4E34 (id_provincia), INDEX id (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter_subscriber (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_401562C3E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE provincias (id INT AUTO_INCREMENT NOT NULL, provincia VARCHAR(255) NOT NULL, id_pais INT NOT NULL, iso VARCHAR(4) NOT NULL, INDEX id (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE socio (id INT AUTO_INCREMENT NOT NULL, imagen_id INT DEFAULT NULL, metodo_pago_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, apellidos VARCHAR(255) NOT NULL, fecha_alta DATE NOT NULL, fecha_baja DATE DEFAULT NULL, fecha_nacimiento DATE NOT NULL, domicilio LONGTEXT NOT NULL, ciudad VARCHAR(255) NOT NULL, codigo_postal VARCHAR(10) NOT NULL, provincia VARCHAR(50) NOT NULL, telefonos LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', email VARCHAR(255) NOT NULL, activo TINYINT(1) NOT NULL, INDEX IDX_38B65309763C8AA7 (imagen_id), INDEX IDX_38B6530934676066 (metodo_pago_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trendix_files (id INT AUTO_INCREMENT NOT NULL, path LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trendix_locations (id INT AUTO_INCREMENT NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_user ADD CONSTRAINT FK_88BDF3E9BF396750 FOREIGN KEY (id) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cuenta_bancaria ADD CONSTRAINT FK_ECD0C9CEDA04E6A9 FOREIGN KEY (socio_id) REFERENCES socio (id)');
        $this->addSql('ALTER TABLE fos_user ADD CONSTRAINT FK_957A64797E9E4C8C FOREIGN KEY (photo_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE municipios ADD CONSTRAINT FK_BBFAB58653AF4E34 FOREIGN KEY (id_provincia) REFERENCES provincias (id)');
        $this->addSql('ALTER TABLE socio ADD CONSTRAINT FK_38B65309763C8AA7 FOREIGN KEY (imagen_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE socio ADD CONSTRAINT FK_38B6530934676066 FOREIGN KEY (metodo_pago_id) REFERENCES metodo_pago (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE socio DROP FOREIGN KEY FK_38B65309763C8AA7');
        $this->addSql('ALTER TABLE app_user DROP FOREIGN KEY FK_88BDF3E9BF396750');
        $this->addSql('ALTER TABLE fos_user DROP FOREIGN KEY FK_957A64797E9E4C8C');
        $this->addSql('ALTER TABLE socio DROP FOREIGN KEY FK_38B6530934676066');
        $this->addSql('ALTER TABLE municipios DROP FOREIGN KEY FK_BBFAB58653AF4E34');
        $this->addSql('ALTER TABLE cuenta_bancaria DROP FOREIGN KEY FK_ECD0C9CEDA04E6A9');
        $this->addSql('DROP TABLE app_user');
        $this->addSql('DROP TABLE cuenta_bancaria');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE gallery');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE metodo_pago');
        $this->addSql('DROP TABLE municipios');
        $this->addSql('DROP TABLE newsletter_subscriber');
        $this->addSql('DROP TABLE provincias');
        $this->addSql('DROP TABLE socio');
        $this->addSql('DROP TABLE trendix_files');
        $this->addSql('DROP TABLE trendix_locations');
    }
}
