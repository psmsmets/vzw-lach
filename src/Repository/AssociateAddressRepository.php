<?php

namespace App\Repository;

use App\Entity\AssociateAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AssociateAddress>
 *
 * @method AssociateAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssociateAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssociateAddress[]    findAll()
 * @method AssociateAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssociateAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssociateAddress::class);
    }

    public function save(AssociateAddress $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AssociateAddress $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
