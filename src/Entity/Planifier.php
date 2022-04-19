<?php

namespace App\Entity;

use App\Repository\PlanifierRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanifierRepository::class)
 */
class Planifier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $duree;

    /**
     * @ORM\ManyToOne(targetEntity=ModuleFormation::class, inversedBy="planifiers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $moduleFormation;

    /**
     * @ORM\ManyToOne(targetEntity=Session::class, inversedBy="planifiers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $session;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDuree(): ?float
    {
        return $this->duree;
    }

    public function setDuree(float $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getModuleFormation(): ?ModuleFormation
    {
        return $this->moduleFormation;
    }

    public function setModuleFormation(?ModuleFormation $moduleFormation): self
    {
        $this->moduleFormation = $moduleFormation;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function __toString()
    {
        return $this->duree;
    }
}
