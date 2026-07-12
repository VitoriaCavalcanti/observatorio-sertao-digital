<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[UniqueEntity(fields: ['slug'])]
class Post
{
    public const STATUS_RASCUNHO = 'rascunho';
    public const STATUS_PUBLICADO = 'publicado';
    public const STATUS_ARQUIVADO = 'arquivado';

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $titulo = '';
    #[ORM\Column(length: 255, unique: true)]
    private string $slug = '';
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank, Assert\Length(max: 255)]
    private string $resumo = '';
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank, Assert\Length(min: 10)]
    private string $conteudo = '';
    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_RASCUNHO;
    #[ORM\Column]
    private bool $publico = false;
    #[ORM\Column]
    private bool $fixado = false;
    #[ORM\Column]
    private int $prioridade = 0;
    #[ORM\Column]
    private \DateTimeImmutable $criadoEm;
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publicadoEm = null;
    #[ORM\Column]
    private \DateTimeImmutable $atualizadoEm;
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $autor = null;
    /** @var Collection<int, Tag> */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'posts', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'post_tag')]
    private Collection $tags;

    public function __construct() { $this->criadoEm = $this->atualizadoEm = new \DateTimeImmutable(); $this->tags = new ArrayCollection(); }
    public function getId(): ?int { return $this->id; }
    public function getTitulo(): string { return $this->titulo; }
    public function setTitulo(string $titulo): static { $this->titulo = $titulo; return $this; }
    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): static { $this->slug = $slug; return $this; }
    public function getResumo(): string { return $this->resumo; }
    public function setResumo(string $resumo): static { $this->resumo = $resumo; return $this; }
    public function getConteudo(): string { return $this->conteudo; }
    public function setConteudo(string $conteudo): static { $this->conteudo = $conteudo; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }
    public function isPublico(): bool { return $this->publico; }
    public function setPublico(bool $publico): static { $this->publico = $publico; return $this; }
    public function isFixado(): bool { return $this->fixado; }
    public function setFixado(bool $fixado): static { $this->fixado = $fixado; return $this; }
    public function getPrioridade(): int { return $this->prioridade; }
    public function setPrioridade(int $prioridade): static { $this->prioridade = $prioridade; return $this; }
    public function getCriadoEm(): \DateTimeImmutable { return $this->criadoEm; }
    public function getPublicadoEm(): ?\DateTimeImmutable { return $this->publicadoEm; }
    public function setPublicadoEm(?\DateTimeImmutable $data): static { $this->publicadoEm = $data; return $this; }
    public function getAtualizadoEm(): \DateTimeImmutable { return $this->atualizadoEm; }
    public function touch(): void { $this->atualizadoEm = new \DateTimeImmutable(); }
    public function getAutor(): ?User { return $this->autor; }
    public function setAutor(User $autor): static { $this->autor = $autor; return $this; }
    /** @return Collection<int, Tag> */
    public function getTags(): Collection { return $this->tags; }
    public function addTag(Tag $tag): static { if (!$this->tags->contains($tag)) $this->tags->add($tag); return $this; }
    public function removeTag(Tag $tag): static { $this->tags->removeElement($tag); return $this; }
}
