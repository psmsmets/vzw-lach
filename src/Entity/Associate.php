<?php

namespace App\Entity;

use App\Entity\AssociateAddress;
use App\Entity\AssociateDetails;
use App\Entity\AssociateMeasurements;
use App\Repository\AssociateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: AssociateRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class Associate
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
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
    private ?bool $viewmaster = null;

    #[ORM\Column]
    private ?bool $onstage = null;

    #[ORM\Column]
    private ?bool $singer = null;

    #[ORM\Column]
    private ?bool $singerSoloist = null;

    #[ORM\Column(length:255, nullable: true)]
    private ?string $companion = null;

    // NOTE: This is not a mapped field of entity metadata, just a simple property.
    #[Vich\UploadableField(mapping: 'associates_portrait', fileNameProperty: 'imagePortrait')]
    #[Assert\File(
        maxSize: '10M',
        mimeTypes: ['image/jpeg'],
        mimeTypesMessage: 'Please upload a valid image',
    )]
    #[Ignore]
    private ?File $imagePortraitFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $imagePortrait = null;

    // NOTE: This is not a mapped field of entity metadata, just a simple property.
    #[Vich\UploadableField(mapping: 'associates_entire', fileNameProperty: 'imageEntire')]
    #[Assert\File(
        maxSize: '10M',
        mimeTypes: ['image/jpeg'],
        mimeTypesMessage: 'Please upload a valid image',
    )]
    #[Ignore]
    private ?File $imageEntireFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $imageEntire = null;

    #[ORM\Column(nullable: true)]
    private ?bool $declarePresent = null;

    #[ORM\Column(nullable: true)]
    private ?bool $declareSecrecy = null;

    #[ORM\Column(nullable: true)]
    private ?bool $declareTerms = null;

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

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'associates')]
    private Collection $users;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    private array $categoryPreferences = [];

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->createdAt = new \DateTimeImmutable();
        $this->enabled = false;
        $this->viewmaster = false;
        $this->onstage = false;
        $this->singer = false;
        $this->singerSoloist = false;
        $this->address = new AssociateAddress($this);
        $this->details = new AssociateDetails($this);
        $this->measurements = new AssociateMeasurements($this);
        $this->categories = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getFullName();
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

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function hasCompletedEnrolment(): ?bool
    {
        return $this->declarePresent & $this->declareSecrecy & $this->declareTerms;
    }

    public function isViewmaster(): ?bool
    {
        return $this->viewmaster;
    }

/* category-based only
    public function setViewmaster(bool $viewmaster): self
    {
        $this->viewmaster = $viewmaster;

        return $this;
    }
*/

    public function setViewmasterFromCategories(): self
    {
        $viewmaster = false;

        foreach ($this->categories as $category) {
            $viewmaster = ($viewmaster or $category->isViewmaster());
        }

        $this->viewmaster = $viewmaster;

        $this->user->setViewmasterFromAssociates();

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

    public function getFullName(bool $reverse=false, string $separator=' '): string
    {
        return $reverse ? $this->lastname.$separator.$this->firstname : $this->firstname.$separator.$this->lastname;
    }

    public function isOnstage(): ?bool
    {
        return $this->onstage;
    }

    public function setOnstage(bool $onstage): self
    {
        $this->onstage = $onstage;

        return $this;
    }

    public function setOnstageFromCategories(): self
    {
        $onstage = false;

        foreach ($this->categories as $category) {
            $onstage = ($onstage or $category->isOnstage());
        }

        $this->onstage = $onstage;

        return $this;
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

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     * @Ignore()
     */
    public function setImagePortraitFile(?File $imagePortraitFile = null): void
    {
        $this->imagePortraitFile = $imagePortraitFile;

        if (null !== $imagePortraitFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImagePortraitFile(): ?File
    {
        return $this->imagePortraitFile;
    }

    public function setImagePortrait(?string $imagePortrait): void
    {
        $this->imagePortrait = $imagePortrait;
    }

    public function getImagePortrait(): ?string
    {
        return $this->imagePortrait;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     * @Ignore()
     */
    public function setImageEntireFile(?File $imageEntireFile = null): void
    {
        $this->imageEntireFile = $imageEntireFile;

        if (null !== $imageEntireFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageEntireFile(): ?File
    {
        return $this->imageEntireFile;
    }

    public function setImageEntire(?string $imageEntire): void
    {
        $this->imageEntire = $imageEntire;
    }

    public function getImageEntire(): ?string
    {
        return $this->imageEntire;
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

    public function isDeclareTerms(): ?bool
    {
        return $this->declareTerms;
    }

    public function setDeclareTerms(bool $declareTerms): self
    {
        $this->declareTerms = $declareTerms;

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
            $this->setOnstageFromCategories();
            $this->setViewmasterFromCategories();
            $this->user->setViewmasterFromAssociates();
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            $this->setOnstageFromCategories();
            $this->setViewmasterFromCategories();
            $this->user->setViewmasterFromAssociates();
        }

        return $this;
    }

    public function getCategoryNames(): string
    {
        $names = [];
        foreach ($this->categories as $category) {
            $names[] = $category->getName();
        }

        return implode(' | ', $names);
    }

    public function getCategoryPreferences(): ?array
    {
        return $this->categoryPreferences;
    }

    public function getCategoryPreferencesList(): string
    {
        return implode(' | ', $this->categoryPreferences);
    }

    public function setCategoryPreferences(array $categoryPreferences): self
    {
        $this->categoryPreferences = $categoryPreferences;

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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function hasUser(User $user): bool
    {
        return $this->users->contains($user);
    }

    public function hasUserIcalToken(string $token): bool
    {
        $match = false;
        foreach ($this->users as $user) {
            $match = ($match or ($user->getIcalToken() === $token));
        }
        return $match;
    }

    public function hasUserCsrfToken(string $token): bool
    {
        $match = false;
        foreach ($this->users as $user) {
            $match = ($match or ($user->getCsrfToken() === $token));
        }
        return $match;
    }

}
