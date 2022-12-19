<?php

namespace App\Service;

use App\Entity\Associate;
use App\Entity\AssociateAddress;
use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\AssociateRepository;
use App\Repository\CategoryRepository;
use App\Repository\EventRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProfileManager 
{
    private $em;
    private $associateRep;
    private $eventRep;
    private $postRep;
    private $userRep;

    public function __construct(
        EntityManagerInterface $em,
        AssociateRepository $associateRepository,
        CategoryRepository $categoryRepository,
        EventRepository $eventRepository,
        PostRepository $postRepository,
        UserRepository $userRepository,
    )
    {
        $this->em = $em;
        $this->associateRepository = $associateRepository;
        $this->categoryRepository = $categoryRepository;
        $this->eventRepository = $eventRepository;
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
    }

    public function associateCategories(Associate $associate): array
    {
        return $this->categoryRepository->findByAssociate($associate);
    }

    public function associateEvents(Associate $associate, ?int $limit = null): array
    {
        return $this->eventRepository->findEvents($associate, null, null, $limit);
    }

    public function associatePosts(Associate $associate, ?int $limit = null): array
    {
        return $this->postRepository->findPosts($associate, null, null, $limit);
    }

    public function categoryPosts(Category $category, ?int $limit = null): array
    {
        return $this->postRepository->findPosts($category, null, null, $limit);
    }

    public function userCategories(User $user): array
    {
        return $this->categoryRepository->findByUser($user);
    }

    public function userEvents(User $user, ?int $limit = null): array
    {
        return $this->eventRepository->findEvents($user, null, null, $limit);
    }

    public function userPosts(User $user, ?int $limit = null): array
    {
        return $this->postRepository->findPosts($user, null, null, $limit);
    }

    public function getEvents($obj, ?int $limit = null): array
    {
        return $this->eventRepository->findEvents($obj, null, null, $limit);
    }

    public function getPosts($obj): array
    {
        return $this->postRepository->findPosts($obj, null, false, Post::NUMBER_OF_ITEMS);
    }

    public function getSpecialPosts($obj): array
    {
        return $this->postRepository->findPosts($obj, true, false, Post::NUMBER_OF_ITEMS_HOMEPAGE);
    }

    public function getPinnedPosts($obj): array
    {
        return $this->postRepository->findPosts($obj, null, true, Post::NUMBER_OF_ITEMS_HOMEPAGE);
    }
}
