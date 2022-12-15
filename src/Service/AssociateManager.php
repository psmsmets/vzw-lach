<?php

namespace App\Service;

use App\Entity\Associate;
use App\Entity\AssociateAddress;
use App\Entity\Event;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\AssociateRepository;
use Doctrine\ORM\EntityManagerInterface;

class AssociateManager 
{
    private $em;
    private $associateRepository;

    public function __construct(EntityManagerInterface $em, AssociateRepository $associateRepository )
    {
        $this->em = $em;
        $this->associateRepository = $associateRepository;
    }
}
