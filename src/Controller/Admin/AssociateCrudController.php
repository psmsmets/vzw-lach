<?php

namespace App\Controller\Admin;

use App\Controller\Admin\CategoryCrudController;
use App\Controller\Admin\UserCrudController;
use App\Entity\Associate;
use App\Entity\AssociateDetails;
use App\Form\AssociateBaseType;
use App\Controller\Admin\Filter\{AssociationDateTimeFilter, AssociationNumericFilter, AssociationTextFilter, GenderFilter};
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, Filters, KeyValueStore};
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\{Field, AssociationField, BooleanField, ChoiceField, DateField, ImageField, NumberField, TextField, EmailField, TelephoneField};
use EasyCorp\Bundle\EasyAdminBundle\Filter\{ArrayFilter};
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
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

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDateFormat('d MMM yy')
            ->setTimeFormat('short')
            ->setDateTimeFormat('medium', 'short')
            ->setTimezone('Europe/Brussels')
            ->setNumberFormat('%.2d')
            ->setDefaultSort(['createdAt' => 'DESC', 'lastname' => 'ASC', 'firstname' => 'ASC'])
            ->setPaginatorPageSize(100)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab('Gegevens');
        yield FormField::addPanel('Gegevens');

        yield BooleanField::new('enabled')->renderAsSwitch(false)->onlyOnIndex();

        yield TextField::new('firstname')->hideOnIndex();
        yield TextField::new('lastname')->hideOnIndex();

        yield DateField::new('details.birthdate')
            ->setFormType(DateType::class)
            ->setFormTypeOptions(['input'  => 'datetime_immutable'])
            ->hideOnIndex()
            ;
        yield ChoiceField::new('details.gender')
            ->setChoices(AssociateDetails::GENDERS)
            ->onlyOnForms()
            ;
        yield NumberField::new('details.age')->onlyOnDetail();
        yield TextField::new('details.genderName', 'Geslacht')->onlyOnDetail();

        yield ImageField::new('imagePortrait', 'Foto 1')
            ->setBasePath('/uploads/associates/portrait')
            ->onlyOnIndex()
            ;
        yield ImageField::new('imagePortrait')
            ->setBasePath('/uploads/associates/portrait')
            ->setColumns(6)
            ->onlyOnDetail()
            ;

        yield ImageField::new('imageEntire', 'Foto 2')
            ->setBasePath('/uploads/associates/entire')
            ->onlyOnIndex()
            ;
        yield ImageField::new('imageEntire')
            ->setBasePath('/uploads/associates/entire')
            ->setColumns(6)
            ->onlyOnDetail()
            ;

        yield TextField::new('firstname')->hideOnDetail();
        yield TextField::new('lastname')->hideOnDetail();

        yield TextField::new('details.gender')->onlyOnIndex();
        yield DateField::new('details.birthdate')
            ->setFormType(DateType::class)
            ->setFormTypeOptions(['input'  => 'datetime_immutable'])
            ->onlyOnIndex()
            ;


        yield Field::new('categoryPreferencesList', 'Voorkeur')->onlyOnDetail();
        yield TextField::new('companion')->hideOnIndex();

        yield BooleanField::new('viewmaster')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('onstage')->renderAsSwitch(false)->hideOnForm();

        yield BooleanField::new('singer')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('singer')->renderAsSwitch(true)->onlyOnForms();

        yield BooleanField::new('singerSoloist')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('singerSoloist')->renderAsSwitch(true)->onlyOnForms();

        yield TextField::new('imagePortraitFile')
            ->setFormType(VichImageType::class)
            //->setColumns(6)
            ->onlyOnForms()
            ;

        yield TextField::new('imageEntireFile')
            ->setFormType(VichImageType::class)
            //->setColumns(6)
            ->onlyOnForms()
            ;

        yield FormField::addTab('Contact');
        yield FormField::addPanel('Contact');

        yield EmailField::new('user.email')->hideOnIndex();
        yield TelephoneField::new('user.phone')->hideOnIndex();

        yield EmailField::new('details.email')->hideOnIndex();
        yield TelephoneField::new('details.phone')->hideOnIndex();
        yield TextField::new('address.address')->onlyOnDetail();

        yield FormField::addTab('Categories');
        yield FormField::addPanel('Categories');

        yield AssociationField::new('categories', 'Groep(en)')
            ->setQueryBuilder(function ($queryBuilder) {
                return $queryBuilder->andWhere('entity.enabled = true'); 

            })
            ->setCrudController(CategoryCrudController::class)
            ->setFormTypeOptions([
                'by_reference' => false,
            ])
            ->autocomplete()
            ;
        yield TextField::new('categoryNames', 'Toegewezen groep(en)')->hideOnForm();
        yield BooleanField::new('onstage')->renderAsSwitch(false)->onlyOnDetail();
        yield TextField::new('role');#->hideOnIndex();
        yield BooleanField::new('measurements.completed', 'Matentabel volledig')->renderAsSwitch(false)->onlyOnIndex();

        yield Field::new('categoryPreferencesList', 'Eigen voorkeur')->hideOnForm();
        yield TextField::new('companion')->onlyOnDetail();

        yield FormField::addTab('Uiterlijk en kledingmaat');
        yield FormField::addPanel('Uiterlijk en kledingmaat');

        yield BooleanField::new('measurements.completed', 'Volledig')->hideOnIndex();
        yield Field::new('measurements.hairColor', 'Haarkleur')->hideOnIndex();
        yield Field::new('measurements.hairType', 'Haartype')->hideOnIndex();
        yield Field::new('measurements.hairLength', 'Haarlengte')->hideOnIndex();
        yield Field::new('measurements.fittingSize', 'Confectiemaat')->hideOnIndex();
        yield Field::new('measurements.height', 'Lengte in cm')->hideOnIndex();
        yield Field::new('measurements.chestGirth', 'Borstomvang in cm')->hideOnIndex();
        yield Field::new('measurements.waistGirth', 'Taille in cm')->hideOnIndex();
        yield Field::new('measurements.hipGirth', 'Heup in cm')->hideOnIndex();

        yield FormField::addTab('Options');

        yield BooleanField::new('enabled')->renderAsSwitch(false)->onlyOnDetail();
        yield BooleanField::new('enabled')->renderAsSwitch(true)->onlyOnForms();
        yield Field::new('id')->onlyOnDetail();
        yield Field::new('createdAt')->hideOnForm();
        yield BooleanField::new('declarePresent', 'Akkoord aanwezig')->hideOnIndex();
        yield BooleanField::new('declareSecrecy', 'Akkoord geheimhouding')->hideOnIndex();
        yield BooleanField::new('declareTerms', 'Akkoord voorwaarden')->hideOnIndex();
        yield Field::new('updatedAt')->hideOnForm();
        yield AssociationField::new('user')
            ->autocomplete()
            ->setCrudController(UserCrudController::class)
            ->setQueryBuilder(function ($queryBuilder) {
                return $queryBuilder->andWhere('entity.enabled = true'); // your query

            })
            ->hideOnIndex()
            ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('createdAt')
            ->add('enabled')
            ->add('singer')
            ->add('singerSoloist')
            ->add(AssociationDateTimeFilter::new('details.birthdate', 'Geboortedatum'))
            ->add(GenderFilter::new('details.gender', 'Geslacht'))//->setFormTypeOption('mapped', false))
            ->add(ArrayFilter::new('categoryPreferences')->setChoices(AssociateBaseType::PREF_CATEGORIES)->setFormTypeOption('mapped', false))
            ->add('categories', 'Toegewezen groep')
        ;
    }
}
