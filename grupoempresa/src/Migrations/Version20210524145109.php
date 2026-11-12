<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210524145109 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user CHANGE photo_id photo_id INT DEFAULT NULL, CHANGE salt salt VARCHAR(255) DEFAULT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE phone phone VARCHAR(255) DEFAULT NULL, CHANGE mobile_phone mobile_phone VARCHAR(255) DEFAULT NULL, CHANGE user_type user_type VARCHAR(255) DEFAULT NULL, CHANGE locale locale VARCHAR(10) DEFAULT \'es\'');
        $this->addSql('ALTER TABLE gallery CHANGE gallery gallery LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE image CHANGE image image VARCHAR(255) DEFAULT NULL, CHANGE original_image original_image VARCHAR(255) DEFAULT NULL, CHANGE upload_dir upload_dir VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE municipios CHANGE id_provincia id_provincia INT DEFAULT NULL');
        $this->addSql('ALTER TABLE socio ADD telefono VARCHAR(255) DEFAULT NULL, ADD telefono2 VARCHAR(255) DEFAULT NULL, DROP telefonos, CHANGE imagen_id imagen_id INT DEFAULT NULL, CHANGE fecha_baja fecha_baja DATE DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user CHANGE photo_id photo_id INT DEFAULT NULL, CHANGE salt salt VARCHAR(255) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE confirmation_token confirmation_token VARCHAR(180) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\', CHANGE name name VARCHAR(255) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`, CHANGE last_name last_name VARCHAR(255) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`, CHANGE phone phone VARCHAR(255) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`, CHANGE mobile_phone mobile_phone VARCHAR(255) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`, CHANGE user_type user_type VARCHAR(255) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`, CHANGE locale locale VARCHAR(10) CHARACTER SET utf8 DEFAULT \'\'\'es\'\'\' COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE gallery CHANGE gallery gallery LONGTEXT CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE image CHANGE image image VARCHAR(255) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`, CHANGE original_image original_image VARCHAR(255) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`, CHANGE upload_dir upload_dir VARCHAR(255) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE municipios CHANGE id_provincia id_provincia INT DEFAULT NULL');
        $this->addSql('ALTER TABLE socio ADD telefonos LONGTEXT CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', DROP telefono, DROP telefono2, CHANGE imagen_id imagen_id INT DEFAULT NULL, CHANGE fecha_baja fecha_baja DATE DEFAULT \'NULL\'');
    }
}
