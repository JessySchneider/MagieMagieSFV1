<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PartieRepository")
 */
class Partie
{
    
    const ETAT_PARTIE_NON_DEMARREE = 'NON_DEMARREE';
    const ETAT_PARTIE_DEMARREE = 'DEMARREE';
    const ETAT_PARTIE_TERMINEE = 'TERMINEE';
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $nom;
    
    /**
     * @ORM\OneToMany(targetEntity="Joueur", mappedBy="partie")
     */
    private $joueurs;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $etat;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ordreActuel;


    public function __construct()
    {
        $this->joueurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }
    
    

    /**
     * @return Collection|Joueur[]
     */
    public function getJoueurs(): Collection
    {
        return $this->joueurs;
    }

    public function addJoueur(Joueur $joueur): self
    {
        if (!$this->joueurs->contains($joueur)) {
            $this->joueurs[] = $joueur;
            $joueur->setPartie($this);
        }

        return $this;
    }

    public function removeJoueur(Joueur $joueur): self
    {
        if ($this->joueurs->contains($joueur)) {
            $this->joueurs->removeElement($joueur);
            // set the owning side to null (unless already changed)
            if ($joueur->getPartie() === $this) {
                $joueur->setPartie(null);
            }
        }

        return $this;
    }

    public function getOrdreActuel(): ?int
    {
        return $this->ordreActuel;
    }

    public function setOrdreActuel(?int $ordreActuel): self
    {
        $this->ordreActuel = $ordreActuel;

        return $this;
    }

}
