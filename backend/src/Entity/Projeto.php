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

    /**
     * @return Collection<int, Indicador>
     */
    public function getIndicadores(): Collection
    {
        return $this->indicadores;
    }

    public function addIndicadore(Indicador $indicadore): static
    {
        if (!$this->indicadores->contains($indicadore)) {
            $this->indicadores->add($indicadore);
            $indicadore->setProjeto($this);
        }

        return $this;
    }

    public function removeIndicadore(Indicador $indicadore): static
    {
        if ($this->indicadores->removeElement($indicadore)) {
            // set the owning side to null (unless already changed)
            if ($indicadore->getProjeto() === $this) {
                $indicadore->setProjeto(null);
            }
        }

        return $this;
    }
}
