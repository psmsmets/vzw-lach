<?php

namespace App\Service;

use App\Entity\Advert;
use App\Entity\Associate;
use App\Entity\AssociateAddress;
use App\Entity\Category;
use App\Entity\Event;
use App\Entity\FAQ;
use App\Entity\Document;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\AdvertRepository;
use App\Repository\AssociateRepository;
use App\Repository\CategoryRepository;
use App\Repository\EventRepository;
use App\Repository\FAQRepository;
use App\Repository\DocumentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Uuid as UuidConstraint;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Uid\Uuid;

class ProfileManager 
{
    public $em;
    public $advertRepository;
    public $associateRepository;
    public $categoryRepository;
    public $eventRepository;
    public $faqRepository;
    public $documentRepository;
    public $postRepository;
    public $userRepository;
    public $requestStack;
    public $router;
    public $security;

    public function __construct(
        EntityManagerInterface $em,
        AdvertRepository $advertRepository,
        AssociateRepository $associateRepository,
        CategoryRepository $categoryRepository,
        EventRepository $eventRepository,
        FAQRepository $faqRepository,
        DocumentRepository $documentRepository,
        PostRepository $postRepository,
        UserRepository $userRepository,
        RequestStack $requestStack,
        UrlGeneratorInterface $router,
        Security $security,
    )
    {
        $this->em = $em;
        $this->advertRepository = $advertRepository;
        $this->associateRepository = $associateRepository;
        $this->categoryRepository = $categoryRepository;
        $this->eventRepository = $eventRepository;
        $this->faqRepository = $faqRepository;
        $this->documentRepository = $documentRepository;
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->security = $security;
        setlocale(LC_ALL, 'nl_BE');
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

    public function getAssociate(Uuid $uuid): ?Associate
    {
        return $this->associateRepository->find($uuid);
    }

    public function getUpcomingEvents($obj, ?int $limit = null): array
    {
        return $this->eventRepository->findEvents($obj, null, null, $limit);
    }

    public function getPeriodEvents($obj, $from = null, $until = null): array
    {
        $from = $from instanceof DateTimeInterface ? $from : new \DateTimeImmutable('2023-01-01');
        $until = $until instanceof DateTimeInterface ? $untill : $from->modify('+1 year');
        return $this->eventRepository->findEvents($obj, $from, $until, null);
    }

    public function getEvent($obj, string $uuid): ?Event
    {
        $validator = Validation::createValidator();
        $uuidContraint = new UuidConstraint();

        $errors = $validator->validate($uuid, $uuidContraint);
        return count($errors) == 0 ? $this->eventRepository->findEvent(Uuid::fromString($uuid), $obj) : null;
    }

    public function getPost($obj, string $uuid): ?Post
    {
        $validator = Validation::createValidator();
        $uuidContraint = new UuidConstraint();

        $errors = $validator->validate($uuid, $uuidContraint);
        return count($errors) == 0 ? $this->postRepository->findPost(Uuid::fromString($uuid), $obj) : null;
    }

    public function getPosts($obj, int $page = 1): array
    {
        return $this->postRepository->findPosts($obj, null, false, Post::NUMBER_OF_ITEMS, $page);
    }

    public function getPostPages($obj): int 
    {
        return (int) ceil($this->postRepository->countPosts($obj, null, false) / Post::NUMBER_OF_ITEMS);
    }

    public function getSpecialPosts($obj): array
    {
        return $this->postRepository->findPosts($obj, true, null, Post::NUMBER_OF_ITEMS_SPECIAL);
    }

    public function getPinnedPosts($obj): array
    {
        return $this->postRepository->findPosts($obj, null, true, Post::NUMBER_OF_ITEMS_PINNED);
    }

    public function getAdvert(string $uuid): ?Advert
    {
        $validator = Validation::createValidator();
        $uuidContraint = new UuidConstraint();

        $errors = $validator->validate($uuid, $uuidContraint);
        return count($errors) == 0 ? $this->advertRepository->findAdvert(Uuid::fromString($uuid)) : null;
    }

    public function getAdverts(int $page = 1): array
    {
        return $this->advertRepository->findAdverts(Advert::NUMBER_OF_ITEMS, $page);
    }

    public function getAdvertPages(): int 
    {
        return (int) ceil($this->advertRepository->countAdverts() / Advert::NUMBER_OF_ITEMS);
    }

    public function getSpecialAdverts(): array
    {
        return $this->advertRepository->findAdverts(Advert::NUMBER_OF_ITEMS_SPECIAL, null, 50);
    }

    public function getFAQ(int $id): ?FAQ
    {
        return $this->faqRepository->findFAQ($id);
    }

    public function getFAQs(): array
    {
        return $this->faqRepository->findFAQs();
    }

    public function getDocument($obj, string $uuid): ?Document
    {
        $validator = Validation::createValidator();
        $uuidContraint = new UuidConstraint();

        $errors = $validator->validate($uuid, $uuidContraint);
        return count($errors) == 0 ? $this->documentRepository->findDocument(Uuid::fromString($uuid), $obj) : null;
    }

    public function getDocuments($obj, int $page = 1): array
    {
        return $this->documentRepository->findDocuments($obj, null, false, Document::NUMBER_OF_ITEMS, $page);
    }

    public function getDocumentPages($obj): int 
    {
        return (int) ceil($this->documentRepository->countDocuments($obj, null, false) / Document::NUMBER_OF_ITEMS);
    }

    public function getSpecialDocuments($obj): array
    {
        return $this->documentRepository->findDocuments($obj, true, null, Document::NUMBER_OF_ITEMS_SPECIAL);
    }

    public function getPinnedDocuments($obj): array
    {
        return $this->documentRepository->findDocuments($obj, null, true, Document::NUMBER_OF_ITEMS_PINNED);
    }

    public function getRequestedPage(Request $request, int $pages): int
    {
        $page = (int) $request->query->get('pagina', 1);
        $page = $page < 1 ? 1 : $page;
        $page = $page > $pages ? $pages : $page;

        return $page;
    }

    public function getBirthdays(): array
    {
        $birthdays = [];

        foreach ($this->security->getUser()->getEnabledAssociates() as $associate) {
            if ($associate->getDetails()->hasBirthday()) $birthdays[] = $associate->getFirstName();
        }

        $session = $this->requestStack->getSession();
        $birthdayWishes = (bool) ($birthdays !== [] and $session->get('birthdays', []) !== $birthdays);

        if ($birthdayWishes) {
            foreach ($birthdays as $birthday) {
                $session->getFlashBag()->add('alert-success', sprintf('Gelukkige verjaardag %s &#x1f973;', $birthday));
            }
            $session->set('birthdays', $birthdays);
        }

        return $birthdays;
    }

    public function verifyAssociates(): void
    {
        $session = $this->requestStack->getSession();
        $verifyAfter = $session->get('verifyAssociatesOnstageAfter', new \DateTime());

        $now = new \DateTime();

        if ($now > $verifyAfter) {
            foreach ($this->security->getUser()->getEnabledAssociates() as $associate) {
                if ($associate->isOnstage() && !$associate->getMeasurements()->hasCompleted()) {
                    $session->getFlashBag()->add(
                        'alert-warning',
                        sprintf(
                            "<strong>%s</strong> heeft een rol op het podium. ".
                            "Hiervoor zijn bijkomende gegevens nodig over het uiterlijk en de kledingmaat. ".
                            "<a href=\"%s\">Klik hier om het profiel aan te vullen.</a>",
                            $associate->getFullName(),
                            $this->router->generate('profile_show', ['id' => $associate->getId()])
                                //.'#uiterlijk-en-kledingmaat'
                        )
                    );
                }
            }
            $session->set('verifyAssociatesOnstageAfter', $now->modify('+1 day'));
        }
    }

    public function getViewpoint()
    {
        $session = $this->requestStack->getSession();
        $viewpoint = $session->get('viewpoint', false);

        if ($viewpoint instanceof Uuid)
        {
            $associate = $this->getAssociate($viewpoint);
            if (!$associate or $associate->getUser() !== $this->security->getUser()) {
                throw $this->createAccessDeniedException();
            }

            return $associate;
        }
        return $this->security->getUser();
    }

    public function setViewpoint($viewpoint): self
    {
        $session = $this->requestStack->getSession();
        if ($viewpoint instanceof Associate)
        {
            $session->set('viewpoint', $viewpoint->getId());
            $session->getFlashBag()->add('alert-success', 'Je bekijkt vanaf nu enkel de informatie voor '.strval($viewpoint));
        } else {
            $session->set('viewpoint', false);
            $session->getFlashBag()->add('alert-success', 'Je bekijkt weer de informatie voor al je deelnemers');
        }

        return $this;
    }
}
