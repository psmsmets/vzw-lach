<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Field\Field;

use App\Controller\Admin\AssociateCrudController;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, Filters, KeyValueStore};
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\{IdField, AssociationField, BooleanField, ChoiceField, EmailField, TextField, TelephoneField};
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
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
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
            ->setPaginatorPageSize(200)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnDetail();

        yield BooleanField::new('enabled')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('enabled')->renderAsSwitch(true)->onlyOnForms();

        yield Field::new('createdAt')->onlyOnDetail();

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
                'ROLE_ADMIN' => 'ROLE_ADMIN',
                'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
            ])
            ->allowMultipleChoices()
            ->autocomplete()
            ->hideOnIndex()
            ;

        yield Field::new('createdAt')->onlyOnIndex();
        yield Field::new('updatedAt')->onlyOnIndex();
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
}
