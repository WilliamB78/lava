<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180420090604 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reservation CHANGE date date LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE room CHANGE comment_state comment_state VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE token_reset_password token_reset_password VARCHAR(255) DEFAULT NULL, CHANGE token_expire token_expire DATETIME DEFAULT NULL, CHANGE is_blocked is_blocked TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reservation CHANGE date date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE room CHANGE comment_state comment_state VARCHAR(150) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user CHANGE token_reset_password token_reset_password VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE token_expire token_expire DATETIME DEFAULT \'NULL\', CHANGE is_blocked is_blocked TINYINT(1) DEFAULT \'NULL\'');
    }
}
