<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'Il y a un compte qui existe déjà avec ce username')]
class User implements  PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type:'string', length: 50, unique: true)]
    #[Assert\NotBlank(message: "Le nom d'utilisateur ne peut pas être vide.")]
    #[Assert\Length(
        min: 6,
        max: 50,
        minMessage: "Le nom d'utilisateur doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom d'utilisateur ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    #[ORM\Column(type:'string', length: 255, nullable: true)]
    #[Assert\Regex(
        pattern: "/^0[1-9](?:[ .-]?\d{2}){4}$/",
        message: "Le numéro de téléphone n'est pas valide."
    )]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: true)]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas un email valide.")]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le mot de passe ne peut pas être vide.")]
    #[Assert\Length(
        min: 6,
        minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères."
    )]
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

    #[ORM\ManyToOne(targetEntity: Campus::class, inversedBy: 'etudiants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    /**
     * @Assert\File(
     *     maxSize = "4M",
     *     maxSizeMessage = "La taille de l'image ne doit pas dépasser 4 Mo.",
     *     mimeTypes = {"image/jpeg", "image/png"},
     *     mimeTypesMessage = "Veuillez télécharger une image valide (JPEG ou PNG).",
     *     minSize = "5B",
     *     minSizeMessage = "L'image doit être d'au moins 5 octets."
     * )
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $profileImage = null;

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
        $roles = ['ROLE_USER'];

        if ($this->isadmin) {
            $roles[] = 'ROLE_ADMIN';
        }

        return $roles;
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

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): self
    {
        $this->profileImage = $profileImage;
        return $this;
    }
}
