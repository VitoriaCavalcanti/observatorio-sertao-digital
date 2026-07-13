<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 80, unique: true)]
    private string $nome = '';
    /** @var Collection<int, Post> */
    #[ORM\ManyToMany(targetEntity: Post::class, mappedBy: 'tags')]
    private Collection $posts;
    public function __construct() { $this->posts = new ArrayCollection(); }
    public function getId(): ?int { return $this->id; }
    public function getNome(): string { return $this->nome; }
    public function setNome(string $nome): static { $this->nome = mb_strtolower(trim($nome)); return $this; }
    public function __toString(): string { return $this->nome; }
}
