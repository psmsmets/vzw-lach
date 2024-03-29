<?php

namespace App\Entity;

use App\Repository\AssociateAddressRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Intl\Countries;

#[ORM\Entity(repositoryClass: AssociateAddressRepository::class)]
class AssociateAddress
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $line1 = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $line2 = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $zip = null;

    #[ORM\Column(length: 28, nullable: true)]
    private ?string $town = null;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $nation = null;

    public function __construct(Associate $associate=null)
    {
      $this->associate = $associate;
      $this->nation = 'BE';
    }

    public function __toString(): string
    {
        return $this->getAddress();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getAssociate(): ?Associate
    {
        return $this->associate;
    }

    public function getAddress(): ?string
    {
        return empty($this->line1) ? 'n/a' : sprintf(
            "%s, %s %s, %s", $this->getLine1(), $this->getZip(), $this->getTown(), $this->getNation()
        );
    }

    public function getLine1(): ?string
    {
        return $this->line1;
    }

    public function setLine1(?string $line1): self
    {
        $this->line1 = $line1;

        return $this;
    }

    public function getLine2(): ?string
    {
        return $this->line2;
    }

    public function setLine2(?string $line2): self
    {
        $this->line2 = $line2;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(?string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function setTown(?string $town): self
    {
        $this->town = $town;

        return $this;
    }

    public function getNation(bool $code = false): ?string
    {
        return $code ? $this->nation : Countries::getName($this->nation);
    }

    public function setNation(?string $nation): self
    {
        $this->nation = $nation;

        return $this;
    }
}
