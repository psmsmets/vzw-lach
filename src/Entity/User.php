<?php

namespace App\Entity;

use App\Entity\Associate;
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
    private ?Uuid $id = null;

    #[ORM\Column]
    private ?bool $enabled = null;

    #[ORM\Column]
    private ?bool $viewmaster = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
        mode: 'strict',
    )]
    private ?string $email = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 64)]
    private ?string $icalToken = null;

    #[ORM\Column(length: 64)]
    private ?string $csrfToken = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $passwordUpdatedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $icalTokenUpdatedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $csrfTokenUpdatedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $lastLoginAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $forcedReloginAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Associate::class)]
    private Collection $associates;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->enabled = true;
        $this->viewmaster = false;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = null;
        $this->password = bin2hex(random_bytes(64)); // null ??
        $this->passwordUpdatedAt = null;
        $this->icalToken = $this->generateToken();
        $this->icalTokenUpdatedAt = null;
        $this->csrfToken = $this->generateToken();
        $this->csrfTokenUpdatedAt = null;
        $this->lastLoginAt = null;
        $this->forcedReloginAt = null;
        $this->associates = new ArrayCollection();
    }

    public function __toString(): string
    {
        return strval($this->getId());
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'enabled' => $this->enabled,
            'viewmaster' => $this->viewmaster,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $this->password,
            'passwordUpdatedAt' => $this->passwordUpdatedAt,
            'icalToken' => $this->icalToken,
            'icalTokenUpdatedAt' => $this->icalTokenUpdatedAt,
            'csrfToken' => $this->csrfToken,
            'csrfTokenUpdatedAt' => $this->csrfTokenUpdatedAt,
            'lastLoginAt' => $this->lastLoginAt,
            'forcedReloginAt' => $this->forcedReloginAt,
            'roles' => $this->roles,
        ];
    }

    public function __unserialize(array $serialized): void 
    {
        $this->id = $serialized['id'];
        $this->enabled = $serialized['enabled'];
        $this->viewmaster = $serialized['viewmaster'];
        $this->createdAt = $serialized['createdAt'];
        $this->updatedAt = $serialized['updatedAt'];
        $this->email = $serialized['email'];
        $this->phone = $serialized['phone'];
        $this->password = $serialized['password'];
        $this->passwordUpdatedAt = $serialized['passwordUpdatedAt'];
        $this->icalToken = $serialized['icalToken'];
        $this->icalTokenUpdatedAt = $serialized['icalTokenUpdatedAt'];
        $this->csrfToken = $serialized['csrfToken'];
        $this->csrfTokenUpdatedAt = $serialized['csrfTokenUpdatedAt'];
        $this->lastLoginAt = $serialized['lastLoginAt'];
        $this->forcedReloginAt = $serialized['forcedReloginAt'];
        $this->roles = $serialized['roles'];
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->setUpdatedAt();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
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

    public function isViewmaster(): ?bool
    {
        return $this->viewmaster;
    }

/* associate-based only
    public function setViewmaster(bool $viewmaster): self
    {
        $this->viewmaster = $viewmaster;

        return $this;
    }
*/

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = strtolower($email);

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

    private function generateToken(): string 
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    public function getIcalToken(): ?string
    {
        return $this->icalToken;
    }

    public function setIcalToken(): self
    {
        $this->icalTokenUpdatedAt = new \DateTimeImmutable();
        $this->icalToken = $this->generateToken();

        return $this;
    }

    public function getCsrfToken(): ?string
    {
        return $this->csrfToken;
    }

    public function setCsrfToken(): self
    {
        $this->csrfTokenUpdatedAt = new \DateTimeImmutable();
        $this->csrfToken = $this->generateToken();

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getPasswordUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->passwordUpdatedAt;
    }

    public function getIcalTokenUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->icalTokenUpdatedAt;
    }

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(): self
    {
        $this->lastLoginAt = new \DateTimeImmutable();

        return $this;
    }

    public function getForcedReloginAt(): ?\DateTimeImmutable
    {
        return $this->forcedReloginAt;
    }

    public function setForcedReloginAt(): self
    {
        $this->forcedReloginAt = new \DateTimeImmutable();

        return $this;
    }

    public function forceRelogin(): self
    {
        $this->setForcedReloginAt();
        $this->setCsrfToken();

        return $this;
    }

    public function setViewmasterFromAssociates(): self
    {
        $viewmaster = false;

        foreach ($this->associates as $associate) {
            $viewmaster = ($viewmaster or $associate->isViewmaster());
        }

        $this->viewmaster = $viewmaster;

        return $this;
    }

    /**
     * @return Collection<int, Associate>
     */
    public function getAssociates(): Collection
    {
        return $this->associates;
    }

    public function getEnabledAssociates(): Collection
    {
        return $this->associates->filter(function(Associate $associate) {
            return $associate->isEnabled() == true;
        });
    }

    public function getDisabledAssociates(): Collection
    {
        return $this->associates->filter(function(Associate $associate) {
            return $associate->isEnabled() == false;
        });
    }

    public function addAssociate(Associate $associate): self
    {
        if (!$this->associates->contains($associate)) {
            $associate->setUser($this);
            $this->associates->add($associate);
            $this->setViewmasterFromAssociates();
        }

        return $this;
    }

    public function removeAssociate(Associate $associate): self
    {
        if ($this->associates->removeElement($associate)) {
            // set the owning side to null (unless already changed)
            if ($associate->getUser() === $this) {
                $associate->setUser(null);
                $this->setViewmasterFromAssociates();
            }
        }

        return $this;
    }

    public function countAssociates(bool $enabled = null): int
    {
        if (is_null($enabled)) return count($this->associates);
        return count($enabled ? $this->getEnabledAssociates() : $this->getDisabledAssociates());
    }

    public function getAssociateNames(int $length = 0, string $separator = ', '): string
    {
        $associateNames = [];
        foreach ($this->associates as $associate) {
            $associateNames[] = $length > 0 ?
                sprintf("%s. %s.",
                    mb_substr($associate->getFirstName(), 0, $length),
                    mb_substr($associate->getLastName(), 0, $length)
                ) : $associate->getFirstName();
        }
        return implode($separator, $associateNames);
    }
}
