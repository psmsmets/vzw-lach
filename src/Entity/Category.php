<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $enabled = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $body = null;

    #[ORM\Column]
    private ?bool $hidden = null;

    #[ORM\Column]
    private ?bool $onstage = null;

    #[ORM\ManyToMany(targetEntity: Associate::class, mappedBy: 'categories', cascade: ['persist'])]
    private Collection $associates;

    #[ORM\ManyToMany(targetEntity: Post::class, mappedBy: 'categories')]
    private Collection $posts;

    #[ORM\ManyToMany(targetEntity: Document::class, mappedBy: 'categories')]
    private Collection $documents;

    public function __construct()
    {
        $this->enabled = true;
        $this->hidden = false;
        $this->onstage = false;
        $this->associates = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    #[ORM\PreUpdate]
    public function preUpdate(): self
    {
        $this->setUpdatedAt();

        foreach ($this->associates as $associate) {
            $associate->setOnstageFromCategories();
        }

        return $this;
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTimeImmutable();

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

    public function isHidden(): ?bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function isOnstage(): ?bool
    {
        return $this->onstage;
    }

    public function setOnstage(bool $onstage): self
    {
        $this->onstage = $onstage;

        foreach ($this->associates as $associate) {
            $associate->setOnstageFromCategories();
        }

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

    public function getAssociateNames(): string
    {
        $names = [];
        foreach ($this->associates as $associate) {
            $names[] = $associate->getFullName($reverse=true);
        }
        asort($names);

        return implode(', ', $names);
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->addCategory($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            $post->removeCategory($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->files;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->files->contains($document)) {
            $this->files->add($documents);
            $document->addCategory($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->files->removeElement($document)) {
            $document->removeCategory($this);
        }

        return $this;
    }
}
