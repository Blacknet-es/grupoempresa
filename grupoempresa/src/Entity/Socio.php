<?php

namespace App\Entity;

use App\Repository\SocioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SocioRepository::class)
 */
class Socio
{
    const PAGO_MENSUAL = 'Mensual';
    const PAGO_TRIMESTRAL = 'Trimestral';
    const PAGO_ANUAL = 'Anual';

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
    private $apellidos;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fecha_alta;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fecha_baja;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fecha_nacimiento;

    /**
     * @ORM\Column(type="text")
     */
    private $domicilio;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ciudad;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $codigo_postal;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $provincia;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $telefono;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $telefono2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activo;

    /**
     * @ORM\ManyToOne(targetEntity=File::class, inversedBy="socios")
     */
    private $imagen;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cuentaBancaria;

    /**
     * @ORM\ManyToOne(targetEntity=MetodoPago::class, inversedBy="socio")
     * @ORM\JoinColumn(nullable=false)
     */
    private $metodoPago;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $dni;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $modalidadPago;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $cuota;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $motivo_baja;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comentarios;

    /**
     * @ORM\Column(type="boolean")
     */
    private $difusion;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $numSocio;

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

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getFechaAlta(): ?\DateTimeInterface
    {
        return $this->fecha_alta;
    }

    public function setFechaAlta(?\DateTimeInterface $fecha_alta): self
    {
        $this->fecha_alta = $fecha_alta;

        return $this;
    }

    public function getFechaBaja(): ?\DateTimeInterface
    {
        return $this->fecha_baja;
    }

    public function setFechaBaja(?\DateTimeInterface $fecha_baja): self
    {
        $this->fecha_baja = $fecha_baja;

        return $this;
    }

    public function getFechaNacimiento(): ?\DateTimeInterface
    {
        return $this->fecha_nacimiento;
    }

    public function setFechaNacimiento(?\DateTimeInterface $fecha_nacimiento): self
    {
        $this->fecha_nacimiento = $fecha_nacimiento;

        return $this;
    }

    public function getDomicilio(): ?string
    {
        return $this->domicilio;
    }

    public function setDomicilio(string $domicilio): self
    {
        $this->domicilio = $domicilio;

        return $this;
    }

    public function getCiudad(): ?string
    {
        return $this->ciudad;
    }

    public function setCiudad(string $ciudad): self
    {
        $this->ciudad = $ciudad;

        return $this;
    }

    public function getCodigoPostal(): ?string
    {
        return $this->codigo_postal;
    }

    public function setCodigoPostal(string $codigo_postal): self
    {
        $this->codigo_postal = $codigo_postal;

        return $this;
    }

    public function getProvincia(): ?string
    {
        return $this->provincia;
    }

    public function setProvincia(string $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getTelefono2(): ?string
    {
        return $this->telefono2;
    }

    public function setTelefono2(?string $telefono): self
    {
        $this->telefono2 = $telefono;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): self
    {
        $this->activo = $activo;

        return $this;
    }

    public function getImagen(): ?File
    {
        return $this->imagen;
    }

    public function setImagen(?File $imagen): self
    {
        $this->imagen = $imagen;

        return $this;
    }

    /**
     * @return string
     */
    public function getCuentaBancaria(): ?string
    {
        return $this->cuentaBancaria;
    }

    public function setCuentaBancaria(?string $cuentaBancaria): self
    {
        $this->cuentaBancaria = $cuentaBancaria;

        return $this;
    }

    public function getMetodoPago(): ?MetodoPago
    {
        return $this->metodoPago;
    }

    public function setMetodoPago(?MetodoPago $metodoPago): self
    {
        $this->metodoPago = $metodoPago;

        return $this;
    }

    public function getDni(): ?string
    {
        return $this->dni;
    }

    public function setDni(string $dni): self
    {
        $this->dni = $dni;

        return $this;
    }

    /**
     * @return string
     */
    public function getModalidadPago()
    {
        return $this->modalidadPago;
    }

    /**
     * @param mixed $modalidadPago
     * @return Socio
     */
    public function setModalidadPago($modalidadPago)
    {
        $this->modalidadPago = $modalidadPago;
        return $this;
    }

    public function getCuota(): ?string
    {
        return $this->cuota;
    }

    public function setCuota(string $cuota): self
    {
        $this->cuota = $cuota;

        return $this;
    }

    public function getMotivoBaja(): ?string
    {
        return $this->motivo_baja;
    }

    public function setMotivoBaja(?string $motivo_baja): self
    {
        $this->motivo_baja = $motivo_baja;

        return $this;
    }

    public function getComentarios(): ?string
    {
        return $this->comentarios;
    }

    public function setComentarios(?string $comentarios): self
    {
        $this->comentarios = $comentarios;

        return $this;
    }

    public function getDifusion(): ?bool
    {
        return $this->difusion;
    }

    public function setDifusion(bool $difusion): self
    {
        $this->difusion = $difusion;

        return $this;
    }

    public function getNumSocio(): ?string
    {
        return $this->numSocio;
    }

    public function setNumSocio(string $numSocio): self
    {
        $this->numSocio = $numSocio;

        return $this;
    }
}
