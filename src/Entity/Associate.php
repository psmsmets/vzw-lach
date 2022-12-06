<?php

namespace App\Entity;

use App\Repository\AssociateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

use App\Entity\AssociateAddress;
use App\Entity\AssociateDetails;
use App\Entity\AssociateMeasurements;

#[ORM\Entity(repositoryClass: AssociateRepository::class)]
class Associate
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column]
    private ?bool $enabled = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column]
    private ?bool $singer = null;

    #[ORM\Column]
    private ?bool $singerSoloist = null;

    #[ORM\Column(length:255, nullable: true)]
    private ?string $companion = null;

    #[ORM\Column]
    private ?bool $declarePresent = null;

    #[ORM\Column]
    private ?bool $declareSecrecy = null;

    #[ORM\Column]
    private ?bool $declareRisks = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $role = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?AssociateAddress $address = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?AssociateDetails $details = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?AssociateMeasurements $measurements = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'associates')]
    private Collection $categories;

    #[ORM\ManyToOne(inversedBy: 'associates')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable("now");
        $this->updatedAt = null;
        $this->enabled = true;
        $this->address = new AssociateAddress($this);
        $this->details = new AssociateDetails($this);
        $this->measurements = new AssociateMeasurements($this);
        $this->categories = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
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
        $this->updatedAt = new \DateTimeImmutable("now");

        return $this;
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getName(bool $reverse=false, string $separator=' '): ?string
    {
        return $reverse ? $this->lastname . $separator . $this->firstname : $this->firstname . $separator . $this->lastname;
    }

    public function isSinger(): ?bool
    {
        return $this->singer;
    }

    public function setSinger(bool $singer): self
    {
        $this->singer = $singer;

        return $this;
    }

    public function isSingerSoloist(): ?bool
    {
        return $this->singerSoloist;
    }

    public function setSingerSoloist(bool $singerSoloist): self
    {
        $this->singerSoloist = $singerSoloist;

        return $this;
    }

    public function getCompanion(): ?string 
    {
        return $this->companion;
    }

    public function setCompanion(?string $companion): self
    {
        $this->companion = $companion;

        return $this;
    }

    public function isDeclarePresent(): ?bool
    {
        return $this->declarePresent;
    }

    public function setDeclarePresent(bool $declarePresent): self
    {
        $this->declarePresent = $declarePresent;

        return $this;
    }

    public function isDeclareSecrecy(): ?bool
    {
        return $this->declareSecrecy;
    }

    public function setDeclareSecrecy(bool $declareSecrecy): self
    {
        $this->declareSecrecy = $declareSecrecy;

        return $this;
    }

    public function isDeclareRisks(): ?bool
    {
        return $this->declareRisks;
    }

    public function setDeclareRisks(bool $declareRisks): self
    {
        $this->declareRisks = $declareRisks;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getDetails(): ?AssociateDetails
    {
        return $this->details;
    }

    public function setDetails(AssociateDetails $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getAddress(): ?AssociateAddress
    {
        return $this->address;
    }

    public function setAddress(AssociateAddress $address): self
    {
        $this->address = $address;

        return $this;
    }


    public function getMeasurements(): ?AssociateMeasurements
    {
        return $this->measurements;
    }

    public function setMeasurements(?AssociateMeasurements $measurements): self
    {
        $this->measurements = $measurements;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
