<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 */
class File
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
     * @ORM\Column(type="text")
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\OneToMany(targetEntity=Socio::class, mappedBy="imagen")
     */
    private $socios;

    public function __construct()
    {
        $this->token = '';
        $this->socios = new ArrayCollection();
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function isImage()
    {
        $extensiones = ['.png', '.jpg', 'jpeg', '.webp', '.tiff', '.svg'];
        foreach ($extensiones as $ext) {
            if (strstr($this->getNombre(), $ext) !== false) {
                return true;
            }
        }

        return false;
    }

    public function getExtension()
    {
        $filePart = explode('.', $this->getNombre());

        if (array_key_exists(1, $filePart)) {
            return $filePart[1];
        }

        return 'FILE';
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return Collection|Socio[]
     */
    public function getSocios(): Collection
    {
        return $this->socios;
    }

    public function addSocio(Socio $socio): self
    {
        if (!$this->socios->contains($socio)) {
            $this->socios[] = $socio;
            $socio->setImagen($this);
        }

        return $this;
    }

    public function removeSocio(Socio $socio): self
    {
        if ($this->socios->removeElement($socio)) {
            // set the owning side to null (unless already changed)
            if ($socio->getImagen() === $this) {
                $socio->setImagen(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nombre;
    }
}
