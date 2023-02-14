<?php

namespace App\Service;

use App\Entity\Associate;
use App\Entity\Category;
use App\Entity\User;

class ProfileViewpoint 
{
    public static function categoriesFilter($qb, $viewpoint): void
    {
        if ($viewpoint instanceof Associate) {
            $qb->setParameter('associate', $viewpoint->getId(), 'uuid');
            $qb->where($qb->expr()->isMemberOf(':associate', 'categories.associates'));
            $count = 0;
            foreach ($viewpoint->getCategories() as $category) {
                foreach ($category->getChildren() as $child) {
                    $qb->setParameter(sprintf('category%d', $count), $child->getId());
                    $qb->orWhere($qb->expr()->isMemberOf(sprintf(':category%d', $count), 'doc.categories'));
                    $count++;
                }
            }
            $qb->orWhere('categories is null');
        }

        if ($viewpoint instanceof Category) {
            $qb->setParameter(':category', $viewpoint);
            $qb->where($qb->expr()->isMemberOf(':category', 'categories'));
            $count = 0;
            foreach ($viewpoint->getChildren() as $child) {
                $qb->setParameter(sprintf('category%d', $count), $child->getId());
                $qb->orWhere($qb->expr()->isMemberOf(sprintf(':category%d', $count), 'doc.categories'));
                $count++;
            }
            $qb->orWhere('categories is null');
        }

        if ($viewpoint instanceof User and !$viewpoint->isViewmaster()) {
            $count = 0;
            foreach ($viewpoint->getEnabledAssociates() as $associate) {
                $qb->setParameter(sprintf('associate%d', $count), $associate->getId(), 'uuid');
                $qb->orWhere($qb->expr()->isMemberOf(sprintf(':associate%d', $count), 'categories.associates'));
                $count++;
            }
            $qb->orWhere('categories is null');
        }
    }
}
