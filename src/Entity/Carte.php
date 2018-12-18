<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CarteRepository")
 */
class Carte
{
    const BAVE_CRAPAUD = "BAVE_CRAPAUD";
    const AILE_CHAUVE_SOURIS = "AILE_CHAUVE_SOURIS";
    const MANDRAGORE = "MANDRAGORE";
    const LAPIS_LAZULI = "LAPIS_LAZULI";
    const CORNE_LICORNE = "CORNE_LICORNE";

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $type;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="Joueur", inversedBy="cartes")
     * @ORM\JoinColumn
     */
    private $joueur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getJoueur(): ?Joueur
    {
        return $this->joueur;
    }

    public function setJoueur(?Joueur $joueur): self
    {
        $this->joueur = $joueur;

        return $this;
    }
}
