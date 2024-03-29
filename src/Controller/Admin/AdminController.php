<?php

namespace App\Controller\Admin;

use App\Entity\Advert;
use App\Entity\Associate;
use App\Entity\Category;
use App\Entity\Document;
use App\Entity\Folder;
use App\Entity\Enrolment;
use App\Entity\Event;
use App\Entity\FAQ;
use App\Entity\Page;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use App\Controller\Admin\AdvertCrudController;
use App\Controller\Admin\AssociateCrudController;
use App\Controller\Admin\CategoryCrudController;
use App\Controller\Admin\DocumentCrudController;
use App\Controller\Admin\EventCrudController;
use App\Controller\Admin\FAQCrudController;
use App\Controller\Admin\PageCrudController;
use App\Controller\Admin\PostCrudController;
use App\Controller\Admin\TagCrudController;
use App\Controller\Admin\UserCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\LocaleDto;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminController extends AbstractDashboardController
{
    #[Route('/beheer', name: 'admin')]
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(AssociateCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css')
            ->addWebpackEncoreEntry('ea-lightbox-prevent-scroll')
            //->addWebpackEncoreEntry('bootstrap')
        ;
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            // the name visible to end users
            ->setTitle('vzw LA:CH')
            // you can include HTML contents too (e.g. to link to an image)
//            ->setTitle('<img src="..."> ACME <span class="text-small">Corp.</span>')

            // by default EasyAdmin displays a black square as its default favicon;
            // use this method to display a custom favicon: the given path is passed
            // "as is" to the Twig asset() function:
            // <link rel="shortcut icon" href="{{ asset('...') }}">
//            ->setFaviconPath('favicon.svg')

            // the domain used by default is 'messages'
//            ->setTranslationDomain('my-custom-domain')

            // set this option if you prefer the page content to span the entire
            // browser width, instead of the default design which sets a max width
            ->renderContentMaximized()

            // set this option if you prefer the sidebar (which contains the main menu)
            // to be displayed as a narrow column instead of the default expanded design
            ->renderSidebarMinimized()

            // by default, users can select between a "light" and "dark" mode for the
            // backend interface. Call this method if you prefer to disable the "dark"
            // mode for any reason (e.g. if your interface customizations are not ready for it)
            //->disableDarkMode()

            // by default, all backend URLs are generated as absolute URLs. If you
            // need to generate relative URLs instead, call this method
            ->generateRelativeUrls()

            // set this option if you want to enable locale switching in dashboard.
            // IMPORTANT: this feature won't work unless you add the {_locale}
            // parameter in the admin dashboard URL (e.g. '/admin/{_locale}').
            // the name of each locale will be rendered in that locale
            // (in the following example you'll see: "English", "Polski")
//            ->setLocales(['nl'])
            // to customize the labels of locales, pass a key => value array
            // (e.g. to display flags; although it's not a recommended practice,
            // because many languages/locales are not associated to a single country)
//            ->setLocales([
//                'nl' => 'Nederlands',
//            ])
            // to further customize the locale option, pass an instance of
            // EasyCorp\Bundle\EasyAdminBundle\Config\Locale
//            ->setLocales([
//                'nl', // locale without custom options
//            ])
        ;
    }

    public function configureMenuItems(): iterable
    {

        return [
            // MenuItem::linkToLogout('Logout', 'fa fa-exit'),
            // MenuItem::linkToDashboard('Dashboard', 'bi bi-house-door-fill'),
            MenuItem::linkToRoute('Frontpage', 'bi bi-house-check-fill', 'home'),

            MenuItem::section('Users')->setPermission('ROLE_ADMIN'),
            MenuItem::linkToCrud('Users', 'bi bi-person-fill-gear', User::class)->setPermission('ROLE_ADMIN'),

            MenuItem::section('Associates')->setPermission('ROLE_ADMIN'),
            MenuItem::linkToCrud('Associates', 'bi bi-person-vcard', Associate::class)->setPermission('ROLE_ADMIN'),

            MenuItem::section('Categories')->setPermission('ROLE_ADMIN'),
            MenuItem::linkToCrud('Categories', 'bi bi-people-fill', Category::class)->setPermission('ROLE_ADMIN'),

            MenuItem::section('Posts')->setPermission('ROLE_ADMIN'),
            MenuItem::linkToCrud('Posts', 'bi bi-card-text', Post::class)->setPermission('ROLE_ADMIN'),

            MenuItem::section('Events')->setPermission('ROLE_ADMIN'),
            MenuItem::linkToCrud('Events', 'bi bi-calendar-week', Event::class)->setPermission('ROLE_ADMIN'),

            MenuItem::section('Enrolments')->setPermission('ROLE_ADMIN'),
            MenuItem::linkToCrud('Enrolments', 'bi bi-check-square-fill', Enrolment::class)->setPermission('ROLE_ADMIN'),

            MenuItem::section('Documents')->setPermission('ROLE_ADMIN'),
            MenuItem::linkToCrud('Documents', 'bi bi-file-earmark-arrow-down', Document::class)->setPermission('ROLE_ADMIN'),
            MenuItem::linkToCrud('Folders', 'bi bi-folder2-open', Folder::class)->setPermission('ROLE_ADMIN'),

            MenuItem::section('Adverts'),
            MenuItem::linkToCrud('Adverts', 'bi bi-search', Advert::class),

            MenuItem::section('Tags'),
            MenuItem::linkToCrud('Tags', 'bi bi-tags', Tag::class),

            MenuItem::section('FAQ')->setPermission('ROLE_ADMIN'),
            MenuItem::linkToCrud('FAQ', 'bi bi-question-square', FAQ::class)->setPermission('ROLE_ADMIN'),

            MenuItem::section('Pages')->setPermission('ROLE_ADMIN'),
            MenuItem::linkToCrud('Pages', 'bi bi-file-earmark-text', Page::class)->setPermission('ROLE_ADMIN'),
        ];
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        // Usually it's better to call the parent method because that gives you a
        // user menu with some menu items already created ("sign out", "exit impersonation", etc.)
        // if you prefer to create the user menu from scratch, use: return UserMenu::new()->...
        return parent::configureUserMenu($user)
            // use the given $user object to get the user name
            ->setName($user->getUserIdentifier())
            // use this method if you don't want to display the name of the user
            ->displayUserName(false)

            // you can return an URL with the avatar image
            //->setAvatarUrl('https://...')
            //->setAvatarUrl($user->getProfileImageUrl())
            // use this method if you don't want to display the user image
            ->displayUserAvatar(false)
            // you can also pass an email address to use gravatar's service
            ->setGravatarEmail($user->getUserIdentifier())

            // you can use any type of menu item, except submenus
            //->addMenuItems([
            //    //MenuItem::linkToRoute('My Profile', 'fa fa-id-card', '...', ['...' => '...']),
            //    MenuItem::linkToRoute('Settings', 'fa fa-user-cog', '...', ['...' => '...']),
            //])
            ;
    }

}
