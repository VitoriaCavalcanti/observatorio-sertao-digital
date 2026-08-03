<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Index(name: 'IDX_CAD_HIST_REG', columns: ['tipo', 'registro_id'])]
class CadastroHistorico
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 20)]
    private string $tipo;
    #[ORM\Column]
    private int $registroId;
    #[ORM\Column(length: 30)]
    private string $acao;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observacao;
    #[ORM\Column(nullable: true)]
    private ?array $dados;
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $responsavel;
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $revisor;
    #[ORM\Column]
    private \DateTimeImmutable $criadoEm;

    public function __construct(string $tipo, int $registroId, string $acao, ?User $responsavel, ?User $revisor, ?string $observacao = null, ?array $dados = null)
    {
        $this->tipo = $tipo; $this->registroId = $registroId; $this->acao = $acao; $this->responsavel = $responsavel; $this->revisor = $revisor; $this->observacao = $observacao; $this->dados = $dados; $this->criadoEm = new \DateTimeImmutable();
    }
    public function getId(): ?int { return $this->id; }
    public function getTipo(): string { return $this->tipo; }
    public function getRegistroId(): int { return $this->registroId; }
    public function getAcao(): string { return $this->acao; }
    public function getObservacao(): ?string { return $this->observacao; }
    public function getDados(): ?array { return $this->dados; }
    public function getResponsavel(): ?User { return $this->responsavel; }
    public function getRevisor(): ?User { return $this->revisor; }
    public function getCriadoEm(): \DateTimeImmutable { return $this->criadoEm; }
}
