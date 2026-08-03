<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260803002100 extends AbstractMigration
{
    public function getDescription(): string { return 'Alinha índices do histórico com o metadata do Doctrine.'; }
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER INDEX IF EXISTS IDX_CAD_HIST_RESP RENAME TO IDX_4C021821BB9AF004');
        $this->addSql('ALTER INDEX IF EXISTS IDX_CAD_HIST_REV RENAME TO IDX_4C021821BD3183DF');
    }
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER INDEX IF EXISTS IDX_4C021821BB9AF004 RENAME TO IDX_CAD_HIST_RESP');
        $this->addSql('ALTER INDEX IF EXISTS IDX_4C021821BD3183DF RENAME TO IDX_CAD_HIST_REV');
    }
}
