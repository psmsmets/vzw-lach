<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, KeyValueStore};
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\{IdField, BooleanField, DateTimeField, SlugField, TextField, TextareaField, TextEditorField};

class PageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Page::class;
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

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnDetail();

        yield BooleanField::new('enabled')->renderAsSwitch(false)->hideOnForm();
        //yield BooleanField::new('enabled')->renderAsSwitch(true)->onlyOnForms();

        yield TextField::new('title', 'Titel');

        yield SlugField::new('slug')->setTargetFieldName('title')->onlyWhenCreating();
        yield TextField::new('slug')->hideWhenCreating()->hideOnForm();

        yield TextEditorField::new('body')->setNumOfRows(30)->hideOnIndex();

        yield DateTimeField::new('createdAt')->onlyOnDetail();

        yield BooleanField::new('showCreatedAt')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('showCreatedAt')->renderAsSwitch(true)->onlyOnForms();

        yield DateTimeField::new('updatedAt')->onlyOnDetail();

        yield BooleanField::new('showUpdatedAt')->renderAsSwitch(false)->onlyOnDetail();
        yield BooleanField::new('showUpdatedAt')->renderAsSwitch(true)->onlyOnForms();

        //yield TextareaField::new('description')->hideOnIndex();
    }
}