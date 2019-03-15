<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UbicazioniRepository")
 */
class Ubicazioni
{
    /**
     * Use constants to define configuration options that rarely change instead
     * of specifying them under parameters section in config/services.yaml file.
     *
     * See https://symfony.com/doc/current/best_practices/configuration.html#constants-vs-configuration-options
     */
    public const NUM_ITEMS = 50;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=13)
     */
    private $codice;

    /**
     * @ORM\Column(type="integer")
     */
    private $fila;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $colonna;

    /**
     * @ORM\Column(type="integer")
     */
    private $piano;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Articoli", inversedBy="ubicazioni")
     */
    private $articolo;

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

    public function getFila(): ?int
    {
        return $this->fila;
    }

    public function setFila(int $fila): self
    {
        $this->fila = $fila;

        return $this;
    }

    public function getColonna(): ?string
    {
        return $this->colonna;
    }

    public function setColonna(string $colonna): self
    {
        $this->colonna = $colonna;

        return $this;
    }

    public function getPiano(): ?int
    {
        return $this->piano;
    }

    public function setPiano(int $piano): self
    {
        $this->piano = $piano;

        return $this;
    }

    public function getArticolo(): ?Articoli
    {
        return $this->articolo;
    }

    public function setArticolo(?Articoli $articolo): self
    {
        $this->articolo = $articolo;

        return $this;
    }
}
