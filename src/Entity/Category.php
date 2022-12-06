<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $enabled = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $body = null;

    #[ORM\Column]
    private ?bool $isHidden = null;

    #[ORM\Column]
    private ?bool $isActor = null;

    #[ORM\ManyToMany(targetEntity: Associate::class, mappedBy: 'categories', cascade: ['persist'])]
    private Collection $associates;

    public function __construct()
    {
        $this->enabled = true;
        $this->associates = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = strtolower($slug);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function isIsHidden(): ?bool
    {
        return $this->isHidden;
    }

    public function setIsHidden(bool $isHidden): self
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    public function isIsActor(): ?bool
    {
        return $this->isActor;
    }

    public function setIsActor(bool $isActor): self
    {
        $this->isActor = $isActor;

        return $this;
    }

    /**
     * @return Collection<Uuid, Associate>
     */
    public function getAssociates(): Collection
    {
        return $this->associates;
    }

    public function addAssociate(Associate $associate): self
    {
        if (!$this->associates->contains($associate)) {
            $this->associates->add($associate);
            $associate->addCategory($this);
        }

        return $this;
    }

    public function removeAssociate(Associate $associate): self
    {
        if ($this->associates->removeElement($associate)) {
            $associate->removeCategory($this);
        }

        return $this;
    }
}
