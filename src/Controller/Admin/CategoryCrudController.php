<?php

namespace App\Controller\Admin;

use App\Controller\Admin\AssociateCrudController;
use App\Entity\Associate;
use App\Entity\Category;
use Doctrine\Common\Collections\Criteria;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, KeyValueStore};
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\{Field, AssociationField, BooleanField, SlugField, TextField, TextareaField};
use Symfony\Component\Form\Extension\Core\Type\DateType;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
            ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN')
            ->disable(Action::DELETE)
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
            ->setPaginatorPageSize(50)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        // yield IdField::new('id')->onlyOnDetail();

        yield BooleanField::new('enabled')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('enabled')->renderAsSwitch(true)->onlyOnForms();

        yield TextField::new('name');

        yield SlugField::new('slug')->setTargetFieldName('name')->hideOnIndex();

        yield TextareaField::new('description')->hideOnIndex();

        yield BooleanField::new('isActor')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('isActor')->renderAsSwitch(true)->onlyOnForms();

        yield BooleanField::new('isHidden')->renderAsSwitch(false)->onlyOnDetail();
        yield BooleanField::new('isHidden')->renderAsSwitch(true)->onlyOnForms();

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

        yield TextField::new('associateNames')->onlyOnDetail();

        yield Field::new('updatedAt')->hideOnForm();

    }
}
