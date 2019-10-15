<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticoliRepository")
 */
class Articoli
{
    /**
     * Use constants to define configuration options that rarely change instead
     * of specifying them under parameters section in config/services.yaml file.
     *
     * See https://symfony.com/doc/current/best_practices/configuration.html#constants-vs-configuration-options
     */
    public const NUM_ITEMS = 30;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=13, unique=true)
     */
    private $codice;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descrizione;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $note;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ubicazioni", mappedBy="articolo")
     */
    private $ubicazioni;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    public function __construct()
    {
        $this->ubicazioni = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodice(): ?string
    {
        return $this->codice;
    }

    public function setCodice(string $codice): self
    {
        $this->codice = $codice;

        return $this;
    }

    public function getDescrizione(): ?string
    {
        return $this->descrizione;
    }

    public function setDescrizione(?string $descrizione): self
    {
        $this->descrizione = $descrizione;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Collection|Ubicazioni[]
     */
    public function getUbicazioni(): Collection
    {
        return $this->ubicazioni;
    }

    public function addUbicazioni(Ubicazioni $ubicazioni): self
    {
        if (!$this->ubicazioni->contains($ubicazioni)) {
            $this->ubicazioni[] = $ubicazioni;
            $ubicazioni->setArticolo($this);
        }

        return $this;
    }

    public function removeUbicazioni(Ubicazioni $ubicazioni): self
    {
        if ($this->ubicazioni->contains($ubicazioni)) {
            $this->ubicazioni->removeElement($ubicazioni);
            // set the owning side to null (unless already changed)
            if ($ubicazioni->getArticolo() === $this) {
                $ubicazioni->setArticolo(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->codice;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

}
