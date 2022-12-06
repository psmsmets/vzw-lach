<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, KeyValueStore};
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\{IdField, AssociationField, BooleanField, SlugField, TextField, TextareaField};
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
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnDetail();

        yield BooleanField::new('enabled')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('enabled')->renderAsSwitch(true)->onlyOnForms();

        yield TextField::new('name');

        yield TextareaField::new('description')->hideOnIndex();

        yield SlugField::new('slug')->setTargetFieldName('name')->hideOnIndex();

        yield BooleanField::new('isActor')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('isActor')->renderAsSwitch(true)->onlyOnForms();

        yield BooleanField::new('isHidden')->renderAsSwitch(false)->onlyOnDetail();
        yield BooleanField::new('isHidden')->renderAsSwitch(true)->onlyOnForms();

        yield AssociationField::new('associates')
            ->setFormTypeOptions([
                'by_reference' => false,
            ])
            ->autocomplete()
            //->renderAsNativeWidget()
            ->hideOnForm()
            ;

    }
}
