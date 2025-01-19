<?php

namespace App\Entity;

use App\Repository\CompteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: CompteRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Compte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?float $solde = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $numero = null;

    #[ORM\Column]
    private ?bool $type = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comptes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): static
    {
        $this->solde = $solde;
        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;
        return $this;
    }

    public function isType(): ?bool
    {
        return $this->type;
    }

    public function setType(bool $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    #[ORM\PrePersist]
    public function generateNumero(): void
    {
        $this->numero = (string) random_int(1000000000, 9999999999);
    }
}

