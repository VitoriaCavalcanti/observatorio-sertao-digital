<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260802234500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adiciona usuário responsável e fluxo de revisão aos cadastros.';
    }

    public function up(Schema $schema): void
    {
        foreach (['instituicao', 'projeto', 'indicador'] as $table) {
            $this->addSql(sprintf("ALTER TABLE %s ADD responsavel_id INT DEFAULT NULL", $table));
            $this->addSql(sprintf("ALTER TABLE %s ADD status_cadastro VARCHAR(20) DEFAULT 'publicado' NOT NULL", $table));
            $this->addSql(sprintf('ALTER TABLE %s ADD CONSTRAINT FK_%s_RESPONSAVEL FOREIGN KEY (responsavel_id) REFERENCES app_user (id) ON DELETE SET NULL', $table, strtoupper($table)));
            $index = ['instituicao' => 'IDX_7CFF8F69BB9AF004', 'projeto' => 'IDX_A0559D94BB9AF004', 'indicador' => 'IDX_CD123EC3BB9AF004'][$table];
            $this->addSql(sprintf('CREATE INDEX %s ON %s (responsavel_id)', $index, $table));
            $this->addSql(sprintf("ALTER TABLE %s ALTER status_cadastro DROP DEFAULT", $table));
        }
    }

    public function down(Schema $schema): void
    {
        foreach (['instituicao', 'projeto', 'indicador'] as $table) {
            $this->addSql(sprintf('ALTER TABLE %s DROP CONSTRAINT FK_%s_RESPONSAVEL', $table, strtoupper($table)));
            $index = ['instituicao' => 'IDX_7CFF8F69BB9AF004', 'projeto' => 'IDX_A0559D94BB9AF004', 'indicador' => 'IDX_CD123EC3BB9AF004'][$table];
            $this->addSql(sprintf('DROP INDEX %s', $index));
            $this->addSql(sprintf('ALTER TABLE %s DROP responsavel_id', $table));
            $this->addSql(sprintf('ALTER TABLE %s DROP status_cadastro', $table));
        }
    }
}
