<?php

namespace App\Repository;

use App\Entity\AssociateDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AssociateDetails>
 *
 * @method AssociateDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssociateDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssociateDetails[]    findAll()
 * @method AssociateDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssociateDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssociateDetails::class);
    }

    public function save(AssociateDetails $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AssociateDetails $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
