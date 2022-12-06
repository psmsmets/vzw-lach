<?php

namespace App\Controller\Admin;

use App\Entity\Associate;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, KeyValueStore};
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\{Field, AssociationField, BooleanField, CollectionField, DateField, TextField};
use Symfony\Component\Form\Extension\Core\Type\DateType;

class AssociateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Associate::class;
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
        yield BooleanField::new('enabled')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('enabled')->renderAsSwitch(true)->onlyOnForms();

        yield Field::new('createdAt')->onlyOnDetail();

        yield TextField::new('firstname');
        yield TextField::new('lastname');

        yield BooleanField::new('singer')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('singer')->renderAsSwitch(true)->onlyOnForms();

        yield BooleanField::new('singerSoloist')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('singerSoloist')->renderAsSwitch(true)->onlyOnForms();

        yield TextField::new('companion');

        yield BooleanField::new('declarePresent')->hideOnIndex();
        yield BooleanField::new('declareSecrecy')->hideOnIndex();
        yield BooleanField::new('declareRisks')->hideOnIndex();

        yield DateField::new('details.birthdate')
            ->setFormType(DateType::class)
            ->setFormTypeOptions(['input'  => 'datetime_immutable'])
            ->hideOnIndex()
            ;

        yield AssociationField::new('categories')
            ->setFormTypeOptions([
                'by_reference' => false,
            ])
            ->autocomplete()
            //->renderAsNativeWidget()
            ;

        yield AssociationField::new('user')->autocomplete()->hideOnIndex();
    }
}
