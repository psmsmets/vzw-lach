<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Html2Text\Html2Text;
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
    private ?\DateTimeImmutable $startTime = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $endTime = null;

    #[ORM\Column]
    private ?bool $allDay = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

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

    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'events')]
    private Collection $tags;

    private $nullifyEndTime = false;
    private $overrule = false;

    public function __construct()
    {
        $this->id = Uuid::v6();
        $this->createdAt = new \DateTimeImmutable();
        $this->publishedAt = \DateTimeImmutable::createFromFormat('Y-m-d H:i', date('Y-m-d H:i'));
        $this->published = false;
        $this->cancelled = false;
        $this->archived = false;
        $this->startTime = new \DateTimeImmutable("today noon"); 
        $this->endTime = $this->startTime->modify('+1 hour');
        $this->allDay = true;
        $this->categories = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf("%s (%s #%d)", $this->startTime->format('Y-m-d'), $this->getTitle(), count($this->categories));
    }

    #[ORM\PreUpdate]
    public function preUpdate()
    {
        $this->setTrueStartTime();
        $this->setTrueEndTime();
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
        $this->published = $this->overrule ? $published : ($published or $this->published);

        return $this;
    }

    public function isDraft(): ?bool
    {
        return !$this->published;
    }

    public function isCancelled(): ?bool
    {
        return $this->cancelled;
    }

    public function setCancelled(bool $cancelled): self
    {
        if ($this->overrule) {
            $this->cancelled = $cancelled;
        } else {
            if (!$this->cancelled && $cancelled === true) {

                $this->cancelled = true;
                $this->cancelledAt = new \DateTimeImmutable();
            }
        }

        return $this;
    }

    public function getCancelledAt(): \DateTimeImmutable
    {
        return $this->cancelledAt;
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
        // remove seconds
        return $this->publishedAt->setTime((int) $this->publishedAt->format('H'), (int) $this->publishedAt->format('i'));
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self
    {
        if (!$this->published) $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getStartTime(): ?\DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeImmutable $startTime): self
    {
        if ($this->published) return $this;

        $this->startTime = $startTime;
        if ($startTime > $this->endTime) $this->endTime = $startTime->modify('+1 hour');

        $this->setUpdatedAt();

        return $this;
    }

    public function trueStartTime(): \DateTimeImmutable
    {
        return $this->allDay ? $this->startTime->setTime(0, 0, 0) : $this->startTime;
    }

    public function calcStartTime(): \DateTimeImmutable
    {
        return $this->trueStartTime();
    }

    private function setTrueStartTime(): self
    {
        $this->startTime = $this->trueStartTime();

        return $this;
    }

    public function getEndTime(): ?\DateTimeImmutable
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeImmutable $endTime): self
    {
        // if ($this->published) return $this;

        if (!is_null($endTime) and $endTime < $this->startTime) {

            $this->endTime = $this->startTime->modify('+1 hour');

        } else {

            $this->endTime = $endTime;

        }
        $this->setUpdatedAt();

        return $this;
    }

    public function trueEndTime(bool $seconds = false): \DateTimeImmutable
    {
        if ( !is_null($this->endTime) and !$this->allDay ) {

            return $this->endTime;

        } else {

            $end = is_null($this->endTime) ? $this->startTime : $this->endTime;

            return $end->setTime(23, 59, $seconds ? 59 : 00);

        }
    }

    public function calcEndTime(): \DateTimeImmutable
    {
        return $this->trueEndTime();
    }

    private function setTrueEndTime(): self
    {
        if (!is_null($this->endTime)) $this->endTime = $this->trueEndTime();

        return $this;
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

    public function getFormattedPeriod(string $locale = 'nl_BE'): ?string
    {
        $fmtD = new \IntlDateFormatter($locale, \IntlDateFormatter::FULL, \IntlDateFormatter::NONE);
        $fmtT = new \IntlDateFormatter($locale, \IntlDateFormatter::NONE, \IntlDateFormatter::SHORT);

        if (is_null($this->endTime)) {

            if ($this->allDay) return $fmtD->format($this->startTime);

            return sprintf(
                "%s vanaf %s",
                $fmtD->format($this->startTime),
                $fmtT->format($this->startTime),
            );

        } else {

            if ($this->isSameDay()) {

                if ($this->allDay) return $fmtD->format($this->startTime);

                return sprintf(
                    "%s van %s tot %s",
                    $fmtD->format($this->startTime),
                    $fmtT->format($this->startTime),
                    $fmtT->format($this->endTime),
                );

            } else {

                if ($this->allDay) return sprintf(
                    "%s tot %s",
                    $fmtD->format($this->startTime),
                    $fmtD->format($this->endTime)
                );

                return sprintf(
                    "%s %s tot %s %s",
                    $fmtD->format($this->startTime),
                    $fmtT->format($this->startTime),
                    $fmtD->format($this->endTime),
                    $fmtT->format($this->endTime),
                );

            }

        }
    }

    public function isAllDay(): ?bool
    {
        return $this->allDay;
    }

    public function getAllDay(): ?bool
    {
        return $this->allDay;
    }

    public function setAllDay(bool $allDay): self
    {
        $this->allDay = $allDay;
        $this->setUpdatedAt();

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
        // if ($this->published) return $this;

        $this->title = $title;
        $this->setUpdatedAt();

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        $this->setUpdatedAt();

        return $this;
    }

    public function getHtml(): ?string
    {
        return $this->body;
    }

    public function getText(): ?string
    {
        if (is_null($this->body) or $this->body === "") return "";

        $html = new \Html2Text\Html2Text($this->body);

        return $html->getText();
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        $this->setUpdatedAt();

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;
        $this->setUpdatedAt();

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        $this->setUpdatedAt();

        return $this;
    }

    /**
     * @return Collection<string, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function getCategoryNames(): string
    {
        $names = [];
        foreach ($this->categories as $category) {
            $names[] = $category->getName();
        }

        return $names ? implode(' | ', $names) : 'iedereen';
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

    public function isOverruled(): ?bool
    {
        return $this->overrule;
    }

    public function setOverruled(bool $overrule): self
    {
        $this->overrule = $overrule;

        return $this;
    }

    public function isNullifyEndTime(): ?bool
    {
        return $this->nullifyEndTime;
    }

    public function setNullifyEndTime(bool $nullifyEndTime): self
    {
        $this->nullifyEndTime = $nullifyEndTime;
        if ($nullifyEndTime) $this->endTime = null;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addEvent($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeEvent($this);
        }

        return $this;
    }

}
