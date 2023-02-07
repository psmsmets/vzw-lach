<?php

namespace App\Entity;

use App\Repository\AssociateMeasurementsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

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

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Range(
        min: 32,
        max: 146,
        notInRangeMessage: 'Fitting size must be between {{ min }} and {{ max }}',
    )]
    private ?int $fittingSize = null; // confectiemaat

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $hairLength = null; // lang, halflang, kort, geen

    public const HAIRLENGTHS = ['geen' => 'geen', 'kort' => 'kort', 'halflang' => 'halflang', 'lang' => 'lang'];

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $hairType = null; // steil, golvend, krullend, kroeshaar

    public const HAIRTYPES = ['steil' => 'steil', 'golvend' => 'golvend', 'krullend' => 'krullend', 'kroeshaar' => 'kroeshaar'];

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $hairColor = null; // kleur haar

    public const HAIRCOLORS = ['zwart' => 'zwart', 'bruin' => 'bruin', 'blond' => 'blond', 'rood' => 'rood', 'grijs' => 'grijs'];

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Range(
        min: 40,
        max: 240,
        notInRangeMessage: 'Height must be between {{ min }}cm and {{ max }}cm',
    )]
    private ?int $height = null; // totale lengte [cm]

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Range(
        min: 30,
        max: 200,
        notInRangeMessage: 'Chest girth must be between {{ min }}cm and {{ max }}cm',
    )]
    private ?int $chestGirth = null; // borstomtrek [cm]

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Range(
        min: 30,
        max: 200,
        notInRangeMessage: 'Waist girth must be between {{ min }}cm and {{ max }}cm',
    )]
    private ?int $waistGirth = null; // tailleomtrek [cm]

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Range(
        min: 30,
        max: 200,
        notInRangeMessage: 'Hip girth must be between {{ min }}cm and {{ max }}cm',
    )]
    private ?int $hipGirth = null; // heupomtrek [cm]

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Range(
        min: 20,
        max: 120,
        notInRangeMessage: 'Inseam length must be between {{ min }}cm and {{ max }}cm',
    )]
    private ?int $inseamLength = null; // binnenbeenlengte [cm]

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Range(
        min: 20,
        max: 120,
        notInRangeMessage: 'Sleeve length must be between {{ min }}cm and {{ max }}cm',
    )]
    private ?int $sleeveLength = null; // armlengte [cm]

    public function __construct(Associate $associate)
    {
      $this->completed = false;
      $this->associate = $associate;
    }

    #[ORM\PreUpdate]
    public function preUpdate(): self
    {
        if (
            !is_null($this->fittingSize) and
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

    public function getFittingSize(): ?int
    {
        return $this->fittingSize;
    }

    public function setFittingSize(?string $fittingSize): self
    {
        $this->fittingSize = $fittingSize;

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

    public function getHairLength(): ?string
    {
        return $this->hairLength;
    }

    public function setHairLength(?string $hairLength): self
    {
        $this->hairLength = $hairLength;

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

    public function getInseam(): ?int
    {
        return $this->inseam;
    }

    public function setInseam(?int $inseam): self
    {
        $this->inseam = $inseam;

        return $this;
    }

    public function getSleeve(): ?int
    {
        return $this->sleeve;
    }

    public function setSleeve(?int $sleeve): self
    {
        $this->sleeve = $sleeve;

        return $this;
    }
}
