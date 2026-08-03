<?php

namespace App\Tests\Entity;

use App\Entity\Indicador;
use App\Entity\Instituicao;
use App\Entity\Projeto;
use App\Entity\CadastroHistorico;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

final class DomainRelationsTest extends TestCase
{
    public function testInstituicaoMantemOProjetoNosDoisLadosDaRelacao(): void
    {
        $instituicao = (new Instituicao())->setNome('Instituto do Sertão');
        $projeto = (new Projeto())->setTitulo('Conecta Sertão');

        self::assertSame(Instituicao::CADASTRO_RASCUNHO, $instituicao->getStatusCadastro());

        $instituicao->addProjeto($projeto);

        self::assertSame($instituicao, $projeto->getInstituicao());
        self::assertTrue($instituicao->getProjetos()->contains($projeto));

        $instituicao->removeProjeto($projeto);

        self::assertNull($projeto->getInstituicao());
        self::assertFalse($instituicao->getProjetos()->contains($projeto));
    }

    public function testProjetoMantemOIndicadorNosDoisLadosDaRelacao(): void
    {
        $projeto = (new Projeto())->setTitulo('Conecta Sertão');
        $indicador = (new Indicador())->setNome('Pessoas formadas')->setValor(120.0);

        self::assertSame(Indicador::CADASTRO_RASCUNHO, $indicador->getStatusCadastro());

        $projeto->addIndicador($indicador);

        self::assertSame($projeto, $indicador->getProjeto());
        self::assertTrue($projeto->getIndicadores()->contains($indicador));

        $projeto->removeIndicador($indicador);

        self::assertNull($indicador->getProjeto());
        self::assertFalse($projeto->getIndicadores()->contains($indicador));
    }

    public function testAlteracaoPendentePreservaEstadoPublicadoERegistraHistorico(): void
    {
        $responsavel = (new User())->setNome('Pessoa')->setEmail('pessoa@example.com');
        $revisor = (new User())->setNome('Revisora')->setEmail('revisora@example.com');
        $instituicao = (new Instituicao())->setNome('Versão pública')->setResponsavel($responsavel)->setStatusCadastro(Instituicao::CADASTRO_PUBLICADO)->setDadosPendentes(['nome' => 'Nova versão'])->setSituacaoRevisao(Instituicao::CADASTRO_EM_ANALISE);
        $historico = new CadastroHistorico('instituicoes', 10, 'enviado', $responsavel, $revisor, 'Conferir nome', $instituicao->getDadosPendentes());

        self::assertSame(Instituicao::CADASTRO_PUBLICADO, $instituicao->getStatusCadastro());
        self::assertSame('Nova versão', $instituicao->getDadosPendentes()['nome']);
        self::assertSame('enviado', $historico->getAcao());
        self::assertSame('Conferir nome', $historico->getObservacao());
        self::assertSame($revisor, $historico->getRevisor());
    }
}
