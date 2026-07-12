<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260711121000 extends AbstractMigration
{
    public function getDescription(): string { return 'Alinha nomes dos índices com o metadata do Doctrine.'; }
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER INDEX IDX_5A8A6C8DF675F31B RENAME TO IDX_5A8A6C8D14D45BBE');
        $this->addSql('ALTER INDEX IDX_5ACE3AF84B89032C RENAME TO IDX_5ACE3AF04B89032C');
        $this->addSql('ALTER INDEX IDX_5ACE3AF8BAD26311 RENAME TO IDX_5ACE3AF0BAD26311');
        $this->addSql('ALTER INDEX UNIQ_389B783C6C6E55B5 RENAME TO UNIQ_389B78354BD530C');
    }
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER INDEX IDX_5A8A6C8D14D45BBE RENAME TO IDX_5A8A6C8DF675F31B');
        $this->addSql('ALTER INDEX IDX_5ACE3AF04B89032C RENAME TO IDX_5ACE3AF84B89032C');
        $this->addSql('ALTER INDEX IDX_5ACE3AF0BAD26311 RENAME TO IDX_5ACE3AF8BAD26311');
        $this->addSql('ALTER INDEX UNIQ_389B78354BD530C RENAME TO UNIQ_389B783C6C6E55B5');
    }
}
