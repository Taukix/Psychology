<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $end_datetime = null;

    #[ORM\Column(length: 100)]
    private ?string $state = null;

    #[ORM\ManyToOne(inversedBy: 'rendezVous')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $rdv_user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTime $start): static
    {
        $this->start = $start;

        $this->end_datetime = (clone $start)->modify('+1 hour');

        return $this;
    }

    public function getEndDatetime(): ?\DateTime
    {
        return $this->end_datetime;
    }

    public function setEndDatetime(\DateTimeInterface $end_datetime): static
    {
        $this->end_datetime = $end_datetime;

        $now = new \DateTime();
        if ($end_datetime < $now) {
            $this->state = 'Passé';
        } else {
            $this->state = 'Validé';
        }

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getRdvUser(): ?Users
    {
        return $this->rdv_user;
    }

    public function setRdvUser(?Users $rdv_user): static
    {
        $this->rdv_user = $rdv_user;

        return $this;
    }
}
