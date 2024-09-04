<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private ?bool $isadmin = false;

    /**
     * @var Collection<int, Excursion>
     */
    #[ORM\OneToMany(targetEntity: Excursion::class, mappedBy: 'organizer')]
    private Collection $excursions_organized;

    /**
     * @var Collection<int, Excursion>
     */
    #[ORM\ManyToMany(targetEntity: Excursion::class, mappedBy: 'participants')]
    private Collection $excursions_participating;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Campus $campus = null;

    public function __construct()
    {
        $this->excursions_organized = new ArrayCollection();
        $this->excursions_participating = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function isadmin(): ?bool
    {
        return $this->isadmin;
    }

    public function setAdmin(bool $isadmin): static
    {
        $this->isadmin = $isadmin;

        return $this;
    }

    /**
     * @return Collection<int, Excursion>
     */
    public function getExcursionsOrganized(): Collection
    {
        return $this->excursions_organized;
    }

    public function addExcursionsOrganized(Excursion $excursionsOrganized): static
    {
        if (!$this->excursions_organized->contains($excursionsOrganized)) {
            $this->excursions_organized->add($excursionsOrganized);
            $excursionsOrganized->setOrganizer($this);
        }

        return $this;
    }

    public function removeExcursionsOrganized(Excursion $excursionsOrganized): static
    {
        if ($this->excursions_organized->removeElement($excursionsOrganized)) {
            // set the owning side to null (unless already changed)
            if ($excursionsOrganized->getOrganizer() === $this) {
                $excursionsOrganized->setOrganizer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Excursion>
     */
    public function getExcursionsParticipating(): Collection
    {
        return $this->excursions_participating;
    }

    public function addExcursionsParticipating(Excursion $excursionsParticipating): static
    {
        if (!$this->excursions_participating->contains($excursionsParticipating)) {
            $this->excursions_participating->add($excursionsParticipating);
            $excursionsParticipating->addParticipant($this);
        }

        return $this;
    }

    public function removeExcursionsParticipating(Excursion $excursionsParticipating): static
    {
        if ($this->excursions_participating->removeElement($excursionsParticipating)) {
            $excursionsParticipating->removeParticipant($this);
        }

        return $this;
    }

    public function getRoles(): array
    {
//        $roles = $this->roles;
//        // guarantee every user at least has ROLE_USER
//        $roles[] = 'ROLE_USER';
//
//        return array_unique($roles);
        return [];
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }
}
