<?php

namespace App\Entity;

use App\Repository\AssociateDetailsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssociateDetailsRepository::class)]
class AssociateDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $secondaryEmail = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $secondaryPhone = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $birthdate = null;

    #[ORM\Column(length: 1)]
    private ?string $gender = null;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $addressNation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $addressStreet = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $addressNumber = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $addressZip = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $addressTown = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSecondaryEmail(): ?string
    {
        return $this->secondaryEmail;
    }

    public function setSecondaryEmail(?string $secondaryEmail): self
    {
        $this->secondaryEmail = $secondaryEmail;

        return $this;
    }

    public function getSecondaryPhone(): ?string
    {
        return $this->secondaryPhone;
    }

    public function setSecondaryPhone(?string $secondaryPhone): self
    {
        $this->secondaryPhone = $secondaryPhone;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeImmutable
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeImmutable $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getAddressNation(): ?string
    {
        return $this->addressNation;
    }

    public function setAddressNation(?string $addressNation): self
    {
        $this->addressNation = $addressNation;

        return $this;
    }

    public function getAddressStreet(): ?string
    {
        return $this->addressStreet;
    }

    public function setAddressStreet(?string $addressStreet): self
    {
        $this->addressStreet = $addressStreet;

        return $this;
    }

    public function getAddressNumber(): ?string
    {
        return $this->addressNumber;
    }

    public function setAddressNumber(?string $addressNumber): self
    {
        $this->addressNumber = $addressNumber;

        return $this;
    }

    public function getAddressZip(): ?string
    {
        return $this->addressZip;
    }

    public function setAddressZip(?string $addressZip): self
    {
        $this->addressZip = $addressZip;

        return $this;
    }

    public function getAddressTown(): ?string
    {
        return $this->addressTown;
    }

    public function setAddressTown(?string $addressTown): self
    {
        $this->addressTown = $addressTown;

        return $this;
    }
}
