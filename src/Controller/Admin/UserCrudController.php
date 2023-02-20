<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Field\Field;

use App\Controller\Admin\AssociateCrudController;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, Filters, KeyValueStore};
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\{BatchActionDto, EntityDto};
use EasyCorp\Bundle\EasyAdminBundle\Field\{FormField, IdField, AssociationField, BooleanField, ChoiceField, EmailField, TextField, TelephoneField};
use EasyCorp\Bundle\EasyAdminBundle\Filter\{BooleanFilter};
use Symfony\Component\Form\Extension\Core\Type\{PasswordType, RepeatedType};
use Symfony\Component\Form\{FormBuilderInterface, FormEvent, FormEvents};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        public UserPasswordHasherInterface $userPasswordHasher
    ) {}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
            ->setPermission(Action::NEW, 'ROLE_ADMIN')
            ->setPermission(Action::EDIT, 'ROLE_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->addBatchAction(Action::new('relogin', 'Force Relogin Users')
                ->linkToCrudAction('forceRelogin')
                ->addCssClass('btn btn-primary')
                ->setIcon('bi bi-person-fill-slash'))
            ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDateFormat('d MMM yy')
            ->setTimeFormat('short')
            ->setDateTimeFormat('medium', 'short')
            ->setTimezone('Europe/Brussels')
            ->setNumberFormat('%.2d')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(100)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab('Gegevens');

        yield IdField::new('id')->onlyOnDetail();

        yield BooleanField::new('enabled')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('enabled')->renderAsSwitch(true)->onlyOnForms();

        yield EmailField::new('email');
        yield TelephoneField::new('phone');

        yield AssociationField::new('associates')
            ->autocomplete()
            ->setCrudController(AssociateCrudController::class)
            ->setFormTypeOptions([
                'by_reference' => false,
            ])
            ->setQueryBuilder(function ($queryBuilder) {
                return $queryBuilder->andWhere('entity.enabled = true'); // your query

            })
            ;

        // password
        yield TextField::new('password')
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => '(Repeat)'],
                'mapped' => false,
            ])
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->onlyOnForms()
            ;

        yield BooleanField::new('isAdmin')->renderAsSwitch(false)->hideOnForm();
        yield ChoiceField::new('roles')
            ->setChoices([
                'ROLE_USER' => 'ROLE_USER',
                'ROLE_MANAGER' => 'ROLE_MANAGER',
                'ROLE_ADMIN' => 'ROLE_ADMIN',
                'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
            ])
            ->allowMultipleChoices()
            ->autocomplete()
            ->hideOnIndex()
            ;
        yield BooleanField::new('viewmaster')->renderAsSwitch(false)->hideOnForm();
        //yield BooleanField::new('viewmaster')->renderAsSwitch(true)->onlyOnForms();

        yield FormField::addTab('Opties')->hideOnForm();

        yield Field::new('createdAt');
        yield Field::new('updatedAt')->onlyOnDetail();
        yield Field::new('lastLoginAt');
        yield Field::new('forcedReloginAt')->onlyOnDetail();
        yield Field::new('passwordUpdatedAt')->onlyOnDetail();
        yield Field::new('icalTokenUpdatedAt')->onlyOnDetail();
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);
        return $this->addPasswordEventListener($formBuilder);
    }

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createEditFormBuilder($entityDto, $formOptions, $context);
        return $this->addPasswordEventListener($formBuilder);
    }

    private function addPasswordEventListener(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
        return $formBuilder->addEventListener(FormEvents::POST_SUBMIT, $this->hashPassword());
    }

    private function hashPassword() {
        return function($event) {

            $form = $event->getForm();
            if (!$form->isValid()) {
                return;
            }
            $user = $form->getData();
            $password = $form->get('password')->getData();
            if ($password === null) {
                return;
            }

            //$hash = $this->userPasswordHasher->hashPassword($this->getUser(), $password);
            $hash = $this->userPasswordHasher->hashPassword($user, $password);
            $form->getData()->setPassword($hash);
        };
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('createdAt')
            ->add('enabled')
        ;
    }

    public function forceUserRelogin(BatchActionDto $batchActionDto)
    {
        $className = $batchActionDto->getEntityFqcn();
        $entityManager = $this->container->get('doctrine')->getManagerForClass($className);
        foreach ($batchActionDto->getEntityIds() as $id) {
            $user = $entityManager->find($className, $id);
            $user->forceRelogin();
        }

        $entityManager->flush();

        return $this->redirect($batchActionDto->getReferrerUrl());
    }
}
