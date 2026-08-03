<?php

namespace App\Entity;

use App\Repository\ProjetoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjetoRepository::class)]
class Projeto
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
    private ?string $titulo = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $resumo = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dataInicio = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dataFim = null;

    #[ORM\ManyToOne(inversedBy: 'projetos')]
    private ?Instituicao $instituicao = null;

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

    /**
     * @var Collection<int, Indicador>
     */
    #[ORM\OneToMany(targetEntity: Indicador::class, mappedBy: 'projeto')]
    private Collection $indicadores;

    public function __construct()
    {
        $this->indicadores = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getResumo(): ?string
    {
        return $this->resumo;
    }

    public function setResumo(?string $resumo): static
    {
        $this->resumo = $resumo;

        return $this;
    }

    public function getDataInicio(): ?\DateTimeImmutable
    {
        return $this->dataInicio;
    }

    public function setDataInicio(?\DateTimeImmutable $dataInicio): static
    {
        $this->dataInicio = $dataInicio;

        return $this;
    }

    public function getDataFim(): ?\DateTimeImmutable
    {
        return $this->dataFim;
    }

    public function setDataFim(?\DateTimeImmutable $dataFim): static
    {
        $this->dataFim = $dataFim;

        return $this;
    }

    public function getInstituicao(): ?Instituicao
    {
        return $this->instituicao;
    }

    public function setInstituicao(?Instituicao $instituicao): static
    {
        $this->instituicao = $instituicao;

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

    /**
     * @return Collection<int, Indicador>
     */
    public function getIndicadores(): Collection
    {
        return $this->indicadores;
    }

    public function addIndicador(Indicador $indicador): static
    {
        if (!$this->indicadores->contains($indicador)) {
            $this->indicadores->add($indicador);
            $indicador->setProjeto($this);
        }

        return $this;
    }

    public function removeIndicador(Indicador $indicador): static
    {
        if ($this->indicadores->removeElement($indicador)) {
            // set the owning side to null (unless already changed)
            if ($indicador->getProjeto() === $this) {
                $indicador->setProjeto(null);
            }
        }

        return $this;
    }
}
