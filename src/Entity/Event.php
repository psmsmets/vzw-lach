<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Event
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column]
    private ?bool $published = null;

    #[ORM\Column]
    private ?bool $cancelled = null;

    #[ORM\Column]
    private ?bool $archived = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $cancelledAt = null;

    #[ORM\Column]
    private ?bool $showUpdatedAt = null;

    #[ORM\Column]
    private ?bool $showPublishedAt = null;

    #[ORM\Column]
    private ?bool $showCancelledAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startTime = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $endTime = null;

    #[ORM\Column]
    private ?bool $allDay = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $body = null;

    #[ORM\Column(length: 255, nullable: true)]
    private $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private $location = null;

    #[ORM\Column(length: 255, nullable: true)]
    private $url = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'events')]
    private Collection $categories;

    public function __construct()
    {
        $this->id = Uuid::v6();
        $this->createdAt = new \DateTimeImmutable();
        $this->publishedAt = \DateTimeImmutable::createFromFormat('Y-m-d H:i', date('Y-m-d H:i'));
        $this->published = false;
        $this->cancelled = false;
        $this->archived = false;
        $this->showUpdatedAt = true;
        $this->showPublishedAt = true;
        $this->showCancelledAt = false;
        $this->categories = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->startTime->format('Y-m-d') . " (" . $this->getTitle() . ")";
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

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function isCancelled(): ?bool
    {
        return $this->cancelled;
    }

    public function setCancelled(bool $cancelled): self
    {
        if (!$this->cancelled && $cancelled === true) {

            $this->cancelled = true;
            $this->cancelledAt = new \DateTimeImmutable();

        }

        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

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

    public function getPublishedAt(): \DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self
    {
        if (!$this->published) $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getShowUpdatedAt(): bool
    {
        return $this->showUpdatedAt;
    }

    public function setShowUpdatedAt(bool $showUpdatedAt): self
    {
        $this->showUpdatedAt = $showUpdatedAt;

        return $this;
    }

    public function getShowPublishedAt(): bool
    {
        return $this->showPublishedAt;
    }

    public function setShowPublishedAt(bool $showPublishedAt): self
    {
        $this->showPublishedAt = $showPublishedAt;

        return $this;
    }

    public function getStartTime(): ?\DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeImmutable $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function trueStartTime(): \DateTimeImmutable
    {
        return $this->allDay ? $this->startTime->setTime(0, 0, 0) : $this->startTime;
    }

    public function getEndTime(): ?\DateTimeImmutable
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeImmutable $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function trueEndTime(): \DateTimeImmutable
    {
        if ( !is_null($this->endTime) and !$this->allDay ) {

            return $this->endTime;

        } else {

            if (is_null($this->endTime)) {

                $end = $this->startTime;

            } else {

                $end = $this->endTime;

            }

            return $end->setTime(23, 59, 59);

        }
    }

    public function calcEndTime(): \DateTimeImmutable
    {
        return $this->trueEndTime();
    }

    public function isFuture(): ?bool
    {
        return new \DateTimeImmutable() < $this->startTime;
    }

    public function isPast(): ?bool
    {
        return new \DateTimeImmutable() > $this->trueEndTime();
    }

    public function isOngoing(): ?bool
    {
        return !$this->isFuture() && !$this->isPast();
    }

    public function getStatus(): ?string
    {
        if ($this->isFuture()) return 'future';
        if ($this->isPast()) return 'past';
        return 'ongoing';
    }

    public function isSameDay(): ?bool
    {
        return is_null($this->endTime)? true : $this->startTime->format('Y-m-d') == $this->endTime->format('Y-m-d');
    }

    public function getFormattedPeriod(): ?string
    {
        $fmt = "%A %e %B %Y";

        if (is_null($this->endTime)) {

            return strftime($this->allDay ? $fmt : $fmt.' vanaf %H:%M', $this->startTime->getTimestamp());

        } else {

            if ($this->isSameDay()) {

                return strftime($fmt.' van %H:%M', $this->startTime->getTimestamp()).strftime(' tot %H:%M', $this->endTime->getTimestamp());

            } else {

                $fmt = $this->allDay ? $fmt : $fmt . ' %H:%M';
                return strftime($fmt, $this->startTime->getTimestamp()).strftime(' tot '.$fmt, $this->endTime->getTimestamp());

            }
        }
    }

    public function getFormattedMonth(): ?string
    {
        return strftime('%B', $this->trueEndTime()->getTimestamp());
    }

    public function getAllDay(): ?bool
    {
        return $this->allDay;
    }

    public function setAllDay(bool $allDay): self
    {
        $this->allDay = $allDay;

        return $this;
    }

    public function getNumberOfDays(): int
    {
        $start = $this->startTime->modify('midnight');
        $end = $this->calcEndTime()->modify('midnight');

        return (int) $start->diff($end)->format('%d') + 1;
    }

    public function getListOfDays(): ?array
    {
        $daylist = array();
        $day = $this->startTime->modify('midnight');

        $numberOfDays = $this->getNumberOfDays();
        if ($numberOfDays > 30) $numberOfDays = 30;

        for ($d=0; $d<$numberOfDays; $d++)
        {
            $daylist[] = $day->modify("+$d days");
        }

        return $daylist;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return Collection<string, Category>
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
        $this->categories->removeElement($category);

        return $this;
    }

}
