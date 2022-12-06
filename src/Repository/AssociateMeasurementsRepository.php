<?php

namespace App\Repository;

use App\Entity\AssociateMeasurements;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AssociateMeasurements>
 *
 * @method AssociateMeasurements|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssociateMeasurements|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssociateMeasurements[]    findAll()
 * @method AssociateMeasurements[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssociateMeasurementsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssociateMeasurements::class);
    }

    public function save(AssociateMeasurements $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AssociateMeasurements $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return AssociateMeasurements[] Returns an array of AssociateMeasurements objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AssociateMeasurements
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
