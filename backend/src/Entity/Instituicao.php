<?php

namespace App\Entity;

use App\Repository\InstituicaoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstituicaoRepository::class)]
class Instituicao
{
    public const CADASTRO_RASCUNHO = 'rascunho';
    public const CADASTRO_EM_ANALISE = 'em_analise';
    public const CADASTRO_PUBLICADO = 'publicado';
    public const CADASTRO_DEVOLVIDO = 'devolvido';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nome = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $sigla = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $tipo = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $site = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $municipio = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $uf = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $responsavel = null;

    #[ORM\Column(length: 20)]
    private string $statusCadastro = self::CADASTRO_RASCUNHO;

    /**
     * @var Collection<int, Projeto>
     */
    #[ORM\OneToMany(targetEntity: Projeto::class, mappedBy: 'instituicao')]
    private Collection $projetos;

    public function __construct()
    {
        $this->projetos = new ArrayCollection();
    }

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

    public function getSigla(): ?string
    {
        return $this->sigla;
    }

    public function setSigla(?string $sigla): static
    {
        $this->sigla = $sigla;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(?string $tipo): static
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getSite(): ?string
    {
        return $this->site;
    }

    public function setSite(?string $site): static
    {
        $this->site = $site;

        return $this;
    }

    public function getMunicipio(): ?string
    {
        return $this->municipio;
    }

    public function setMunicipio(?string $municipio): static
    {
        $this->municipio = $municipio;

        return $this;
    }

    public function getUf(): ?string
    {
        return $this->uf;
    }

    public function setUf(?string $uf): static
    {
        $this->uf = $uf;

        return $this;
    }

    public function getResponsavel(): ?User { return $this->responsavel; }
    public function setResponsavel(?User $responsavel): static { $this->responsavel = $responsavel; return $this; }
    public function getStatusCadastro(): string { return $this->statusCadastro; }
    public function setStatusCadastro(string $status): static { $this->statusCadastro = $status; return $this; }

    /**
     * @return Collection<int, Projeto>
     */
    public function getProjetos(): Collection
    {
        return $this->projetos;
    }

    public function addProjeto(Projeto $projeto): static
    {
        if (!$this->projetos->contains($projeto)) {
            $this->projetos->add($projeto);
            $projeto->setInstituicao($this);
        }

        return $this;
    }

    public function removeProjeto(Projeto $projeto): static
    {
        if ($this->projetos->removeElement($projeto)) {
            // set the owning side to null (unless already changed)
            if ($projeto->getInstituicao() === $this) {
                $projeto->setInstituicao(null);
            }
        }

        return $this;
    }
}
