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

    public const GENDERS_ = ['m' => 'man', 'v' => 'vrouw', 'x' => 'genderneutraal'];
    public const GENDERS  = ['man' => 'm', 'vrouw' => 'v', 'genderneutraal' => 'x'];

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
        $this->email = strtolower($email);

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

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

    public function getBirthday(): ?\DateTimeImmutable
    {
        return new \DateTimeImmutable(sprintf("this year %s", $this->birthdate->format('m/d')));
    }

    public function hasBirthday(): bool
    {
        if (is_null($this->birthdate)) return false;

        $today = (int) (new \DateTimeImmutable())->format('z');
        $bday = (int) $this->birthdate->format('z');

        return $bday === $today;
    }

    public function getBirthyear(): ?int
    {
        return $this->birthdate ? (int) $this->birthdate->format('Y') : null;
    }

    public function getAge($ref = new \DateTimeImmutable()): ?int
    {
        return $this->birthdate ? (int) $ref->diff($this->birthdate)->y : null;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function getGenderName(): ?string
    {
        return $this->gender ? self::GENDERS_[$this->gender] : null;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }
}
