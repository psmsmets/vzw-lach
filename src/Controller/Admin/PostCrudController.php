<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, KeyValueStore};
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\{AssociationField, IdField, BooleanField, DateTimeField, SlugField, TextField, TextareaField, TextEditorField};
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
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
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab('What');
        yield FormField::addPanel('What');

        yield IdField::new('id')->onlyOnDetail();

        yield TextField::new('title');
        yield SlugField::new('slug')->setTargetFieldName('title')->hideOnIndex();

        yield TextEditorField::new('body')
            ->setTrixEditorConfig([
                'blockAttributes' => [
                    'default' => ['tagName' => 'p'],
                    'heading1' => ['tagName' => 'h3'],
                ],
                'css' => [
                    'attachment' => 'bootstrap.css',
                ],
            ])
            ->setNumOfRows(20)
            ->onlyOnForms()
            ;

        yield FormField::addTab('Who');
        yield FormField::addPanel('Who');

        yield AssociationField::new('categories')->autocomplete()->hideOnIndex();

        yield FormField::addTab('Options');
        yield FormField::addPanel('Options');

        yield DateTimeField::new('publishedAt')->setRequired(false);
        yield BooleanField::new('published')->renderAsSwitch(true)->onlyOnForms();
        yield BooleanField::new('published')->renderAsSwitch(false)->hideOnForm();

        yield DateTimeField::new('createdAt')->onlyOnDetail();

        yield DateTimeField::new('updatedAt')->hideOnForm();
        yield BooleanField::new('showUpdatedAt')->renderAsSwitch(true)->hideOnIndex();

        yield BooleanField::new('special')->renderAsSwitch(true);
        yield BooleanField::new('pinned')->renderAsSwitch(true);
        yield BooleanField::new('archived')->renderAsSwitch(true)->hideOnIndex();

    }
}
