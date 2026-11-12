<?php

namespace App\Entity;

use App\Repository\MetodoPagoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MetodoPagoRepository::class)
 */
class MetodoPago
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $clave;

    /**
     * @ORM\OneToMany(targetEntity=Socio::class, mappedBy="metodoPago")
     */
    private $socio;

    /**
     * @ORM\Column(type="integer")
     */
    private $periodo;

    public function __construct()
    {
        $this->socio = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getClave(): ?string
    {
        return $this->clave;
    }

    public function setClave(string $clave): self
    {
        $this->clave = $clave;

        return $this;
    }

    /**
     * @return Collection|Socio[]
     */
    public function getSocio(): Collection
    {
        return $this->socio;
    }

    public function addSocio(Socio $socio): self
    {
        if (!$this->socio->contains($socio)) {
            $this->socio[] = $socio;
            $socio->setMetodoPago($this);
        }

        return $this;
    }

    public function removeSocio(Socio $socio): self
    {
        if ($this->socio->removeElement($socio)) {
            // set the owning side to null (unless already changed)
            if ($socio->getMetodoPago() === $this) {
                $socio->setMetodoPago(null);
            }
        }

        return $this;
    }

    public function getPeriodo(): ?int
    {
        return $this->periodo;
    }

    public function setPeriodo(int $periodo): self
    {
        $this->periodo = $periodo;

        return $this;
    }

    public function __toString()
    {
        return $this->getNombre();
    }
}
