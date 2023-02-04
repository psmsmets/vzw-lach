<?php

namespace App\Entity;

use App\Repository\AdvertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdvertRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Advert 
{
    public const NUMBER_OF_ITEMS_SPECIAL = 5;
    public const NUMBER_OF_ITEMS = 25;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column]
    private ?bool $published = null;

    #[ORM\Column(nullable: true)]
    private ?bool $completed = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $required = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $acquired = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    private ?int $progress = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $body = null;

    public function __construct()
    {
        $this->id = Uuid::v6();
        $this->createdAt = new \DateTimeImmutable();
        $this->publishedAt = \DateTimeImmutable::createFromFormat('Y-m-d H:i', date('Y-m-d H:i'));
        $this->published = true;
        $this->completed = false;
        $this->progress = 0;
    }

    public function __toString(): string
    {
        return sprintf('%s (%d%%)', $this->getTitle(), $this->getProgress());
    }

    #[ORM\PreUpdate]
    public function preUpdate()
    {
        $this->setUpdatedAt();
        $this->setProgress();
        if (!is_null($this->required)) $this->setCompleted($this->acquired >= $this-> required);
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function isCompleted(): ?bool
    {
        return is_null($this->completed) ? $this->getProgress() == 100 : $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;
        if ($completed) $this->completedAt = new \DateTimeImmutable();

        return $this;
    }

    public function setRequired(int $required): self
    {
        $this->required = $required;

        return $this;
    }

    public function getRequired(): ?int
    {
        return $this->required;
    }

    public function setAcquired(int $acquired): self
    {
        $this->acquired = $acquired;

        return $this;
    }

    public function getAcquired(): ?int
    {
        return $this->acquired;
    }

    public function setProgress(): self
    {
        if (is_null($this->required) || $this->required == 0)
        {
            $this->progess = $this->completed ? 100 : 0;
        } else {
            $this->progress = (int) round($this->acquired/$this->required*100);
        }

        return $this;
    } 

    public function getProgress(): int
    {
        return $this->progress;
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

    public function getPublishedAt(): \DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self
    {
        if (!$this->published) $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getCompletedAt(): \DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

}
