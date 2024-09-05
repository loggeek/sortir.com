<?php

namespace App\DTO;

use App\Entity\Campus;
use DateTimeInterface;

class ExcursionFilter
{
    private ?Campus $campus;
    private ?string $name;
    private ?DateTimeInterface $datemin;
    private ?DateTimeInterface $datemax;
    private bool $organizer;
    private bool $participating;
    private bool $nonparticipating;
    private bool $showpast;

    public function __construct(
        ?Campus            $campus = null,
        ?string            $name = null,
        ?DateTimeInterface $datemin = null,
        ?DateTimeInterface $datemax = null,
        bool               $organizer = false,
        bool               $participating = false,
        bool               $nonparticipating = false,
        bool               $showpast = false
    ) {
        $this->campus = $campus;
        $this->name = $name;
        $this->datemin = $datemin;
        $this->datemax = $datemax;
        $this->organizer = $organizer;
        $this->participating = $participating;
        $this->nonparticipating = $nonparticipating;
        $this->showpast = $showpast;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): void
    {
        $this->campus = $campus;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDatemin(): ?DateTimeInterface
    {
        return $this->datemin;
    }

    public function setDatemin(?DateTimeInterface $datemin): void
    {
        $this->datemin = $datemin;
    }

    public function getDatemax(): ?DateTimeInterface
    {
        return $this->datemax;
    }

    public function setDatemax(?DateTimeInterface $datemax): void
    {
        $this->datemax = $datemax;
    }

    public function isOrganizer(): bool
    {
        return $this->organizer;
    }

    public function setOrganizer(bool $organizer): void
    {
        $this->organizer = $organizer;
    }

    public function isParticipating(): bool
    {
        return $this->participating;
    }

    public function setParticipating(bool $participating): void
    {
        $this->participating = $participating;
    }

    public function isNonparticipating(): bool
    {
        return $this->nonparticipating;
    }

    public function setNonparticipating(bool $nonparticipating): void
    {
        $this->nonparticipating = $nonparticipating;
    }

    public function isShowpast(): bool
    {
        return $this->showpast;
    }

    public function setShowpast(bool $showpast): void
    {
        $this->showpast = $showpast;
    }
}