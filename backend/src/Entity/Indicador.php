<?php

namespace App\Entity;

use App\Repository\IndicadorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IndicadorRepository::class)]
class Indicador
{
    public const CADASTRO_RASCUNHO = 'rascunho';
    public const CADASTRO_EM_ANALISE = 'em_analise';
    public const CADASTRO_PUBLICADO = 'publicado';
    public const CADASTRO_DEVOLVIDO = 'devolvido';
    public const CADASTRO_REJEITADO = 'rejeitado';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nome = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descricao = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $unidade = null;

    #[ORM\Column(nullable: true)]
    private ?float $valor = null;

    #[ORM\Column(nullable: true)]
    private ?int $anoReferencia = null;

    #[ORM\ManyToOne(inversedBy: 'indicadores')]
    private ?Projeto $projeto = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $responsavel = null;

    #[ORM\Column(length: 20)]
    private string $statusCadastro = self::CADASTRO_RASCUNHO;

    #[ORM\Column(nullable: true)]
    private ?array $dadosPendentes = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $situacaoRevisao = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observacaoRevisao = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): static
    {
        $this->nome = $nome;

        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): static
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getUnidade(): ?string
    {
        return $this->unidade;
    }

    public function setUnidade(?string $unidade): static
    {
        $this->unidade = $unidade;

        return $this;
    }

    public function getValor(): ?float
    {
        return $this->valor;
    }

    public function setValor(?float $valor): static
    {
        $this->valor = $valor;

        return $this;
    }

    public function getAnoReferencia(): ?int
    {
        return $this->anoReferencia;
    }

    public function setAnoReferencia(?int $anoReferencia): static
    {
        $this->anoReferencia = $anoReferencia;

        return $this;
    }

    public function getProjeto(): ?Projeto
    {
        return $this->projeto;
    }

    public function setProjeto(?Projeto $projeto): static
    {
        $this->projeto = $projeto;

        return $this;
    }

    public function getResponsavel(): ?User { return $this->responsavel; }
    public function setResponsavel(?User $responsavel): static { $this->responsavel = $responsavel; return $this; }
    public function getStatusCadastro(): string { return $this->statusCadastro; }
    public function setStatusCadastro(string $status): static { $this->statusCadastro = $status; return $this; }
    public function getDadosPendentes(): ?array { return $this->dadosPendentes; }
    public function setDadosPendentes(?array $dados): static { $this->dadosPendentes = $dados; return $this; }
    public function getSituacaoRevisao(): ?string { return $this->situacaoRevisao; }
    public function setSituacaoRevisao(?string $situacao): static { $this->situacaoRevisao = $situacao; return $this; }
    public function getObservacaoRevisao(): ?string { return $this->observacaoRevisao; }
    public function setObservacaoRevisao(?string $observacao): static { $this->observacaoRevisao = $observacao; return $this; }
}
