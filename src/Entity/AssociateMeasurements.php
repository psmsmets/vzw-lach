<?php

namespace App\Entity;

use App\Repository\AssociateMeasurementsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AssociateMeasurementsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AssociateMeasurements
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column]
    private ?bool $completed = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $shoeSize = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $headGirth = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $size = null; // confectiemaat

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $hairType = null; // lang of kort haar

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $hairColor = null; // kleur haar

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $height = null; // totale lengte [cm]

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $chestGirth = null; // borstomtrek [cm]

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $waistGirth = null; // tailleomtrek [cm]

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $hipGirth = null; // heupomtrek [cm]

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $detail4 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $detail5 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $detail6 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $detail7 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $detail8 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $detail9 = null;

    public function __construct(Associate $associate)
    {
      $this->completed = false;
      $this->associate = $associate;
    }

    #[ORM\PreUpdate]
    public function preUpdate(): self
    {
        if (
            !is_null($this->size) and
            !is_null($this->hairType) and
            !is_null($this->hairColor) and
            !is_null($this->height) and
            !is_null($this->chestGirth) and
            !is_null($this->waistGirth) and
            !is_null($this->hipGirth)
        ) {
            $this->setCompleted(true);
        }

        return $this;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function hasCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    public function getAssociate(): ?Associate
    {
        return $this->associate;
    }

    public function getShoeSize(): ?int
    {
        return $this->shoeSize;
    }

    public function setShoeSize(?int $shoeSize): self
    {
        $this->shoeSize = $shoeSize;

        return $this;
    }

    public function getHeadGirth(): ?int
    {
        return $this->headGirth;
    }

    public function setHeadGirth(?int $headGirth): self
    {
        $this->headGirth = $headGirth;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getHairType(): ?string
    {
        return $this->hairType;
    }

    public function setHairType(?string $hairType): self
    {
        $this->hairType = $hairType;

        return $this;
    }

    public function getHairColor(): ?string
    {
        return $this->hairColor;
    }

    public function setHairColor(?string $hairColor): self
    {
        $this->hairColor = $hairColor;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getChestGirth(): ?int
    {
        return $this->chestGirth;
    }

    public function setChestGirth(?int $chestGirth): self
    {
        $this->chestGirth = $chestGirth;

        return $this;
    }

    public function getWaistGirth(): ?int
    {
        return $this->waistGirth;
    }

    public function setWaistGirth(?int $waistGirth): self
    {
        $this->waistGirth = $waistGirth;

        return $this;
    }

    public function getHipGirth(): ?int
    {
        return $this->hipGirth;
    }

    public function setHipGirth(?int $hipGirth): self
    {
        $this->hipGirth = $hipGirth;

        return $this;
    }

    public function getDetail4(): ?int
    {
        return $this->detail4;
    }

    public function setDetail4(?int $detail4): self
    {
        $this->detail4 = $detail4;

        return $this;
    }

    public function getDetail5(): ?int
    {
        return $this->detail5;
    }

    public function setDetail5(?int $detail5): self
    {
        $this->detail5 = $detail5;

        return $this;
    }

    public function getDetail6(): ?int
    {
        return $this->detail6;
    }

    public function setDetail6(?int $detail6): self
    {
        $this->detail6 = $detail6;

        return $this;
    }

    public function getDetail7(): ?int
    {
        return $this->detail7;
    }

    public function setDetail7(?int $detail7): self
    {
        $this->detail7 = $detail7;

        return $this;
    }

    public function getDetail8(): ?int
    {
        return $this->detail8;
    }

    public function setDetail8(?int $detail8): self
    {
        $this->detail8 = $detail8;

        return $this;
    }

    public function getDetail9(): ?int
    {
        return $this->detail9;
    }

    public function setDetail9(?int $detail9): self
    {
        $this->detail9 = $detail9;

        return $this;
    }
}
