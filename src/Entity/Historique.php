<?php

namespace App\Entity;

use App\Repository\HistoriqueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueRepository::class)]
class Historique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'historiques')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'historiques')]
    private ?Mission $mission = null;

    #[ORM\Column(nullable: true)]
    private ?bool $resultat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMission(): ?Mission
    {
        return $this->mission;
    }

    public function setMission(?Mission $mission): static
    {
        $this->mission = $mission;

        return $this;
    }

    public function isResultat(): ?bool
    {
        return $this->resultat;
    }

    public function setResultat(?bool $resultat): static
    {
        $this->resultat = $resultat;

        return $this;
    }
}