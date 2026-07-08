<?php

namespace App\Entity;

use App\Repository\IndicadorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IndicadorRepository::class)]
class Indicador
{
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
}
