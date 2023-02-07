<?php

namespace App\Repository;

use App\Entity\FAQ;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FAQ>
 *
 * @method FAQ|null find($id, $lockMode = null, $lockVersion = null)
 * @method FAQ|null findOneBy(array $criteria, array $orderBy = null)
 * @method FAQ[]    findAll()
 * @method FAQ[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FAQRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FAQ::class);
    }

    public function save(FAQ $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FAQ $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Advert[] Returns a Advert objects
     */
    public function findFAQ(int $id): ?FAQ
    {
        $qb = $this->createQueryBuilder('faq');

        $qb->setParameter('enabled', true);
        $qb->andWhere('faq.enabled = :enabled');

        $qb->setParameter('id', $id);
        $qb->andWhere('faq.id = :id');

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return FAQ[] Returns an array of FAQ objects
     */
    public function findFAQs(): array
    {
        $qb = $this->createQueryBuilder('faq');

        $qb->setParameter('enabled', true);
        $qb->andWhere('faq.enabled = :enabled');

        //$qb->addOrderBy('faq.enabled', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function countFAQs(): int
    {
        $qb = $this->createQueryBuilder('faq');

        $qb->setParameter('enabled', true);
        $qb->andWhere('faq.enabled = :enabled');

        return count($qb->getQuery()->getResult());
    }
}
