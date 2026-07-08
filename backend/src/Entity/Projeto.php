<?php

namespace App\Entity;

use App\Repository\ProjetoRepository;
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
}
