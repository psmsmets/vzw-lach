<?php

namespace App\Controller\Admin;

use App\Entity\Associate;
use App\Controller\Admin\Filter\{AssociationDateTimeFilter, AssociationNumericFilter, AssociationTextFilter, GenderFilter};

use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, Filters, KeyValueStore};
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\{Field, AssociationField, BooleanField, CollectionField, DateField, ImageField, NumberField, TextField, EmailField, TelephoneField};
use EasyCorp\Bundle\EasyAdminBundle\Filter\{ArrayFilter, ChoiceFilter, DatetimeFilter, EntityFilter, NumericFilter};

use Symfony\Component\Form\Extension\Core\Type\DateType;

use Vich\UploaderBundle\Form\Type\VichImageType;

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
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield BooleanField::new('completedEnrolment')->renderAsSwitch(false)->hideOnForm();

        yield BooleanField::new('enabled')->renderAsSwitch(false)->onlyOnDetail();
        yield BooleanField::new('enabled')->renderAsSwitch(true)->onlyOnForms();

        yield Field::new('createdAt')->onlyOnDetail();

        yield ImageField::new('imagePortrait')
            ->setBasePath('/uploads/associates/portrait')
            ->hideOnForm()
            ;
/*
        yield ImageField::new('imagePortraitFile', 'Foto portrait')
            ->setFormType(VichImageType::class)
            ->setUploadDir('public_html/uploads/associates/portrait')
            ->onlyOnForms()
            ;
*/

        yield ImageField::new('imageEntire')
            ->setBasePath('/uploads/associates/entire')
            ->hideOnForm()
            ;
/*
        yield ImageField::new('imageEntireFile', 'Foto volledig')
            ->setFormType(VichImageType::class)
            ->setUploadDir('public_html/uploads/associates/entire')
            ->onlyOnForms()
            ;
*/

        yield TextField::new('firstname');
        yield TextField::new('lastname');

        yield TextField::new('categoryPreferencesList')->hideOnForm();

        yield BooleanField::new('singer')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('singer')->renderAsSwitch(true)->onlyOnForms();

        yield BooleanField::new('singerSoloist')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('singerSoloist')->renderAsSwitch(true)->onlyOnForms();

        yield TextField::new('companion');

        //yield BooleanField::new('declarePresent', 'Akkoord aanwezig')->hideOnIndex();
        //yield BooleanField::new('declareSecrecy', 'Akkoord geheimhouding')->hideOnIndex();
        //yield BooleanField::new('declareTerms', 'Akkoord voorwaarden')->hideOnIndex();

        yield DateField::new('details.birthdate')
            ->setFormType(DateType::class)
            ->setFormTypeOptions(['input'  => 'datetime_immutable'])
            //->hideOnIndex()
            ;
        yield NumberField::new('details.age')->onlyOnDetail();
        yield TextField::new('details.gender')->hideOnForm();
        yield EmailField::new('details.email')->hideOnIndex();
        yield TelephoneField::new('details.phone')->hideOnIndex();

        yield TextField::new('address.address')->onlyOnDetail();

        yield AssociationField::new('categories')
            ->setFormTypeOptions([
                'by_reference' => false,
            ])
            ->autocomplete()
            ->hideOnIndex()
            ;

        yield AssociationField::new('user')->autocomplete()->hideOnIndex();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('enabled')
            ->add('categories')
            //->add(ChoiceFilter::new('categoryPreferencesList'))
            ->add(AssociationDateTimeFilter::new('details.birthdate'))
            ->add(GenderFilter::new('details.gender'))//->setFormTypeOption('mapped', false))
        ;
    }
}
