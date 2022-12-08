<?php

namespace App\Entity;

use App\Repository\AssociateDetailsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: AssociateDetailsRepository::class)]
class AssociateDetails
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $birthdate = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $gender = null;

    public function __construct(Associate $associate)
    {
      $this->associate = $associate;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getAssociate(): ?Associate
    {
        return $this->associate;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->Email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->Phone = $phone;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeImmutable
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeImmutable $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getBirthyear(): ?int
    {
        return (int) $this->birthdate->format('Y');
    }

    public function getAge($ref = new \DateTimeImmutable("01-08-2023")): ?int
    {
        return (int) $ref->diff($this->birthdate)->y;
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
}
