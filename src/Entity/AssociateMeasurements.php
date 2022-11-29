<?php

namespace App\Entity;

use App\Repository\AssociateMeasurementsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssociateMeasurementsRepository::class)]
class AssociateMeasurements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $shoeSize = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $headGirth = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $size = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $hairType = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $hairColor = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $detail0 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $detail1 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $detail2 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $detail3 = null;

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

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $detail10 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $detail11 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $detail12 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $detail13 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $detail14 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $detail15 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $detail16 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $detail17 = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDetail0(): ?int
    {
        return $this->detail0;
    }

    public function setDetail0(?int $detail0): self
    {
        $this->detail0 = $detail0;

        return $this;
    }

    public function getDetail1(): ?int
    {
        return $this->detail1;
    }

    public function setDetail1(?int $detail1): self
    {
        $this->detail1 = $detail1;

        return $this;
    }

    public function getDetail2(): ?int
    {
        return $this->detail2;
    }

    public function setDetail2(?int $detail2): self
    {
        $this->detail2 = $detail2;

        return $this;
    }

    public function getDetail3(): ?int
    {
        return $this->detail3;
    }

    public function setDetail3(?int $detail3): self
    {
        $this->detail3 = $detail3;

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

    public function getDetail10(): ?int
    {
        return $this->detail10;
    }

    public function setDetail10(?int $detail10): self
    {
        $this->detail10 = $detail10;

        return $this;
    }

    public function getDetail11(): ?int
    {
        return $this->detail11;
    }

    public function setDetail11(?int $detail11): self
    {
        $this->detail11 = $detail11;

        return $this;
    }

    public function getDetail12(): ?int
    {
        return $this->detail12;
    }

    public function setDetail12(?int $detail12): self
    {
        $this->detail12 = $detail12;

        return $this;
    }

    public function getDetail13(): ?int
    {
        return $this->detail13;
    }

    public function setDetail13(?int $detail13): self
    {
        $this->detail13 = $detail13;

        return $this;
    }

    public function getDetail14(): ?int
    {
        return $this->detail14;
    }

    public function setDetail14(?int $detail14): self
    {
        $this->detail14 = $detail14;

        return $this;
    }

    public function getDetail15(): ?int
    {
        return $this->detail15;
    }

    public function setDetail15(?int $detail15): self
    {
        $this->detail15 = $detail15;

        return $this;
    }

    public function getDetail16(): ?int
    {
        return $this->detail16;
    }

    public function setDetail16(?int $detail16): self
    {
        $this->detail16 = $detail16;

        return $this;
    }

    public function getDetail17(): ?int
    {
        return $this->detail17;
    }

    public function setDetail17(?int $detail17): self
    {
        $this->detail17 = $detail17;

        return $this;
    }
}
