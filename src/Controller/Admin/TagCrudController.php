<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, KeyValueStore};
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\{Field, AssociationField, BooleanField, IdField, SlugField, TextField, TextareaField};
use Symfony\Component\Form\Extension\Core\Type\DateType;

class TagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tag::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
            ->setPermission(Action::NEW, 'ROLE_ADMIN')
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
        yield IdField::new('id')->hideOnForm();

        yield BooleanField::new('enabled')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('enabled')->renderAsSwitch(true)->onlyOnForms();

        yield TextField::new('name');

        yield SlugField::new('slug')->setTargetFieldName('name')->hideOnIndex();

        //yield TextareaField::new('description')->hideOnIndex();

        yield AssociationField::new('events')->autocomplete();
        yield AssociationField::new('posts')->autocomplete();
        yield AssociationField::new('folders')->autocomplete();
        yield AssociationField::new('documents')->autocomplete();
        yield AssociationField::new('adverts')->autocomplete();

        yield Field::new('updatedAt')->hideOnForm();

    }
}
