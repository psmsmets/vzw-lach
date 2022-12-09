<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
//    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
//    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
        mode: 'strict',
    )]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private ?bool $enabled = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $passwordUpdatedAt = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $phone = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Associate::class)]
    private Collection $associates;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = null;
        $this->enabled = true;
        $this->passwordUpdatedAt = null;
        $this->password = bin2hex(random_bytes(64));
        $this->associates = new ArrayCollection();
    }

    public function __toString(): string
    {
//        return $this->email;
        return strval($this->getId());
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'enabled' => $this->enabled,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $this->password,
            'passwordUpdatedAt' => $this->passwordUpdatedAt,
            'roles' => $this->roles,
        ];
    }

    public function __unserialize(array $serialized): void 
    {
        $this->id = $serialized['id'];
        $this->enabled = $serialized['enabled'];
        $this->createdAt = $serialized['createdAt'];
        $this->updatedAt = $serialized['updatedAt'];
        $this->email = $serialized['email'];
        $this->phone = $serialized['phone'];
        $this->password = $serialized['password'];
        $this->passwordUpdatedAt = $serialized['passwordUpdatedAt'];
        $this->roles = $serialized['roles'];
    }

    #[ORM\PreUpdate]
    public function preUpdate()
    {
        $this->setUpdatedAt();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function isAdmin(): bool
    {
        foreach ($this->roles as $role) {
            if ($role === 'ROLE_ADMIN' or $role === 'ROLE_SUPER_ADMIN') return true;
        }
        return false;
    }

    public function isSuperAdmin(): bool
    {
        foreach ($this->roles as $role) {
            if ($role === 'ROLE_SUPER_ADMIN') return true;
        }
        return false;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->passwordUpdatedAt = new \DateTimeImmutable();
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getPasswordUpdatedAt(): ?\DateTimeInterface
    {
        return $this->passwordUpdatedAt;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, Associate>
     */
    public function getAssociates(): Collection
    {
        return $this->associates;
    }

    public function addAssociate(Associate $associate): self
    {
        if (!$this->associates->contains($associate)) {
            $associate->setUser($this);
            $this->associates->add($associate);
        }

        return $this;
    }

    public function removeAssociate(Associate $associate): self
    {
        if ($this->associates->removeElement($associate)) {
            // set the owning side to null (unless already changed)
            if ($associate->getUser() === $this) {
                $associate->setUser(null);
            }
        }

        return $this;
    }

    public function countAssociates(): int
    {
        return count($this->associates);
    }

    public function getAssociateNames(int $length = 0, string $separator = ', '): string
    {
        $associateNames = [];
        foreach ($this->associates as $associate) {
            $associateNames[] = $length > 0 ? mb_substr($associate->getFirstName(), 0, $length) . '. ' . mb_substr($associate->getLastName(), 0, $length) . '.'  : $associate->getFirstName();
        }
        return implode($separator, $associateNames);
    }
}
