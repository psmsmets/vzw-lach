<?php

namespace App\Controller\Admin;

use App\Entity\Associate;
use App\Form\AssociateBaseType;
use App\Controller\Admin\Filter\{AssociationDateTimeFilter, AssociationNumericFilter, AssociationTextFilter, GenderFilter};

use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, Filters, KeyValueStore};
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\{Field, AssociationField, BooleanField, CollectionField, DateField, ImageField, NumberField, TextField, EmailField, TelephoneField};
use EasyCorp\Bundle\EasyAdminBundle\Filter\{ChoiceFilter};
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
        yield TextField::new('details.gender')->onlyOnForms();
        yield NumberField::new('details.age')->onlyOnDetail();
        yield TextField::new('details.genderName', 'Geslacht')->onlyOnDetail();

        yield ImageField::new('imagePortrait', 'Foto')
            ->setBasePath('/uploads/associates/portrait/thumbs')
            ->onlyOnIndex()
            ;

        yield ImageField::new('imagePortrait')
            ->setBasePath('/uploads/associates/portrait')
            ->setColumns(6)
            ->onlyOnDetail()
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

        yield TextField::new('categoryPreferencesList', 'Voorkeur')->hideOnForm();
        yield TextField::new('companion')->hideOnIndex();

        yield BooleanField::new('singer')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('singer')->renderAsSwitch(true)->onlyOnForms();

        yield BooleanField::new('singerSoloist')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('singerSoloist')->renderAsSwitch(true)->onlyOnForms();

        yield FormField::addTab('Contact');
        yield FormField::addPanel('Contact');

        yield EmailField::new('user.email')->hideOnIndex();
        yield TelephoneField::new('user.phone')->hideOnIndex();

        yield EmailField::new('details.email')->hideOnIndex();
        yield TelephoneField::new('details.phone')->hideOnIndex();
        yield TextField::new('address.address')->onlyOnDetail();

        yield FormField::addTab('Categories');
        yield FormField::addPanel('Categories');

        yield TextField::new('categoryPreferencesList', 'Voorkeur')->onlyOnDetail();
        yield TextField::new('companion')->onlyOnDetail();

        yield AssociationField::new('categories', 'Toegewezen groep(en)')
            ->setFormTypeOptions([
                'by_reference' => false,
            ])
            ->autocomplete()
            ->hideOnIndex()
            ;
        yield TextField::new('categoryNames', 'Toegewezen groep(en)')->onlyOnDetail();

        yield FormField::addTab('Options');

        yield BooleanField::new('enabled')->renderAsSwitch(false)->onlyOnDetail();
        yield BooleanField::new('enabled')->renderAsSwitch(true)->onlyOnForms();
        yield Field::new('id')->onlyOnDetail();
        yield Field::new('createdAt')->onlyOnDetail();
        yield BooleanField::new('declarePresent', 'Akkoord aanwezig')->hideOnIndex();
        yield BooleanField::new('declareSecrecy', 'Akkoord geheimhouding')->hideOnIndex();
        yield BooleanField::new('declareTerms', 'Akkoord voorwaarden')->hideOnIndex();
        yield Field::new('updatedAt')->onlyOnDetail();
        yield AssociationField::new('user')->autocomplete()->hideOnIndex();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('enabled')
            ->add('singer')
            ->add('singerSoloist')
            ->add(AssociationDateTimeFilter::new('details.birthdate', 'Geboortedatum'))
            ->add(GenderFilter::new('details.gender', 'Geslacht'))//->setFormTypeOption('mapped', false))
            ->add(ChoiceFilter::new('categoryPreferences')->setChoices(AssociateBaseType::PREF_CATEGORIES))
            ->add('categories', 'Toegewezen groep')
        ;
    }
/*
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        // if user defined sort is not set
        if (0 === count($searchDto->getSort())) {
            $queryBuilder
                ->addSelect('CONCAT(entity.first_name, \' \', entity.last_name) AS HIDDEN full_name')
                ->addOrderBy('full_name', 'DESC');
        }

        return $queryBuilder;
    }
*/
}
