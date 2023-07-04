<?php

namespace App\Entity;

use App\Repository\EnrolmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EnrolmentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Enrolment
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?bool $enrolled = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $cancelledAt = null;

    #[ORM\Column]
    private ?bool $cancelled = null;

    #[ORM\Column(nullable: true)]
    private ?float $charge = null;

    #[ORM\Column]
    private ?bool $paid = null;

    #[ORM\Column(length: 255, nullable: true)]
    private $option1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private $option2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private $option3 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private $note = null;

    #[ORM\ManyToOne(inversedBy: 'enrolments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    #[ORM\ManyToOne(inversedBy: 'enrolments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Associate $associate = null;

    public function __construct(Event $event = null, Associate $associate = null)
    {
        $this->id = Uuid::v6();
        $this->enrolled = false;
        $this->cancelled = false;
        $this->event = $event;
        $this->associate = $associate;
        $this->paid = $event ? $event->getEnrolFreeOfCharge() : null;
    }

    public function __toString(): string
    {
        return sprintf("%s %s", $this->associate->getFullName(), $this->event->getTitle());
    }

    #[ORM\PrePersist]
    public function prePersist()
    {
        $this->enrolled = true;
        $this->createdAt = new \DateTimeImmutable();
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

    public function isEnrolled(): ?bool
    {
        return $this->enrolled;
    }

    public function setEnrolled(bool $enrolled): self
    {
        $this->enrolled = $enrolled;

        return $this;
    }

    public function hasCancelled(): ?bool
    {
        return $this->cancelled;
    }

    public function setCancelled(bool $cancelled): self
    {
        $this->cancelled = $cancelled;
        $this->cancelledAt = new \DateTimeImmutable();

        return $this;
    }

    public function getCancelledAt(): \DateTimeImmutable
    {
        return $this->cancelledAt;
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

    public function getOption1(): ?string
    {
        return $this->option1;
    }

    public function setOption1(string $option1): self
    {
        $this->option1 = $option1;
        $this->setUpdatedAt();

        return $this;
    }

    public function getOption2(): ?string
    {
        return $this->option2;
    }

    public function setOption2(string $option2): self
    {
        $this->option2 = $option1;
        $this->setUpdatedAt();

        return $this;
    }

    public function getOption3(): ?string
    {
        return $this->option3;
    }

    public function setOption3(string $option3): self
    {
        $this->option3 = $option3;
        $this->setUpdatedAt();

        return $this;
    }

    public function hasPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;
        $this->setUpdatedAt();

        return $this;
    }

    public function getTotalCharge(): ?float
    {
        return $this->charge;
    }

    public function setTotalCharge(float $charge): self
    {
        $this->charge = $charge;
        $this->setUpdatedAt();

        return $this;
    }

    public function hasNote(): bool
    {
        return !is_null($this->note) or $this->note != '';
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;
        $this->setUpdatedAt();

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getAssociate(): ?Associate
    {
        return $this->associate;
    }

    public function setAssociate(?Associate $associate): static
    {
        $this->associate = $associate;

        return $this;
    }

}
