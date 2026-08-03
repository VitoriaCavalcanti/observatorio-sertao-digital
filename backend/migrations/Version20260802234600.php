<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260802234600 extends AbstractMigration
{
    public function getDescription(): string { return 'Alinha os índices de responsável com o metadata do Doctrine.'; }
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER INDEX IF EXISTS IDX_INSTITUICAO_RESPONSAVEL RENAME TO IDX_7CFF8F69BB9AF004');
        $this->addSql('ALTER INDEX IF EXISTS IDX_PROJETO_RESPONSAVEL RENAME TO IDX_A0559D94BB9AF004');
        $this->addSql('ALTER INDEX IF EXISTS IDX_INDICADOR_RESPONSAVEL RENAME TO IDX_CD123EC3BB9AF004');
    }
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER INDEX IF EXISTS IDX_7CFF8F69BB9AF004 RENAME TO IDX_INSTITUICAO_RESPONSAVEL');
        $this->addSql('ALTER INDEX IF EXISTS IDX_A0559D94BB9AF004 RENAME TO IDX_PROJETO_RESPONSAVEL');
        $this->addSql('ALTER INDEX IF EXISTS IDX_CD123EC3BB9AF004 RENAME TO IDX_INDICADOR_RESPONSAVEL');
    }
}
