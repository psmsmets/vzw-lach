<?php

namespace App\Repository;

use App\Entity\Post;
use App\Service\ProfileViewpoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function save(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Post[] Returns a Post objects
     */
    public function findPost(Uuid $uuid, $obj = null): ?Post
    {
        $qb = $this->createQueryBuilder('post');

        $qb->leftJoin('post.categories','categories');
        $qb->addSelect('categories');
        ProfileViewpoint::categoriesFilter($qb, $obj);

        $qb->setParameter('published', true);
        $qb->andWhere('post.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('post.publishedAt <= :now');

        $qb->setParameter('uuid', $uuid, 'uuid');
        $qb->andWhere('post.id = :uuid');

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Post[] Returns an array of Post objects
     */
    public function findPosts($obj = null, ?bool $special = null, ?bool $pinned = null, $limit = null, $page = 1): array
    {
        $limit = is_null($limit) ? Post::NUMBER_OF_ITEMS : $limit;
        $offset = ( $page < 1 ? 0 : $page - 1 ) * Post::NUMBER_OF_ITEMS;

        $qb = $this->createQueryBuilder('post');

        $qb->leftJoin('post.categories','categories');
        $qb->addSelect('categories');
        ProfileViewpoint::categoriesFilter($qb, $obj);

        $qb->setParameter('published', true);
        $qb->andWhere('post.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('post.publishedAt <= :now');

        if (!is_null($special)) {
            $qb->setParameter('special', $special);
            $qb->andWhere('post.special = :special');
        }

        if (!is_null($pinned)) {
            $qb->setParameter('pinned', $pinned);
            $qb->andWhere('post.pinned = :pinned');
        }

        $qb->orderBy('post.publishedAt', 'DESC');
        $qb->setFirstResult($offset);
        //$qb->setMaxResults($limit);

        return array_slice($qb->getQuery()->getResult(), 0, $limit);
    }

    public function countPosts($obj = null, ?bool $special = null, ?bool $pinned = null): int
    {
        $qb = $this->createQueryBuilder('post');

        $qb->leftJoin('post.categories','categories');
        $qb->addSelect('categories');
        ProfileViewpoint::categoriesFilter($qb, $obj);

        $qb->setParameter('published', true);
        $qb->andWhere('post.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('post.publishedAt <= :now');

        if (!is_null($special)) {
            $qb->setParameter('special', $special);
            $qb->andWhere('post.special = :special');
        }

        if (!is_null($pinned)) {
            $qb->setParameter('pinned', $pinned);
            $qb->andWhere('post.pinned = :pinned');
        }

        return count($qb->getQuery()->getResult());
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        if (isset($criteria['id']) && is_array($criteria['id'])) {
            $ids = [];
            foreach ($criteria['id'] as $id) {
                if (Uuid::isValid($id)) {
                    $ids[] = Uuid::fromString($id)->toBinary();
                } else {
                    $ids[] = $id;
                }
            }
            $criteria['id'] = $ids;
        }
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }
}
