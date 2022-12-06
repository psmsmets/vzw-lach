<?php

namespace App\Controller\Admin;

use App\Entity\Associate;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, KeyValueStore};
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
//use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\{Field, AssociationField, BooleanField, CollectionField, DateField, ImageField, TextField};
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
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield BooleanField::new('enabled')->renderAsSwitch(false)->onlyOnDetail();
        yield BooleanField::new('enabled')->renderAsSwitch(true)->onlyOnForms();

        yield Field::new('createdAt')->onlyOnDetail();

        yield ImageField::new('imagePortrait', 'Foto portrait')
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

        yield ImageField::new('imageEntire', 'Foto volledig')
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

        yield TextField::new('firstname', 'Voornaam');
        yield TextField::new('lastname', 'Familienaam');

        yield TextField::new('categoryPreferencesList', 'Voorkeur')->hideOnForm();

        yield BooleanField::new('singer', 'Zanger')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('singer', 'Zanger')->renderAsSwitch(true)->onlyOnForms();

        yield BooleanField::new('singerSoloist', 'Zanger solist')->renderAsSwitch(false)->hideOnForm();
        yield BooleanField::new('singerSoloist', 'Zanger solist')->renderAsSwitch(true)->onlyOnForms();

        yield TextField::new('companion', 'Samen met ...');

        yield BooleanField::new('declarePresent', 'Akkoord aanwezig')->hideOnIndex();
        yield BooleanField::new('declareSecrecy', 'Akkoord geheimhouding')->hideOnIndex();
        yield BooleanField::new('declareTerms', 'Akkoord voorwaarden')->hideOnIndex();

        yield DateField::new('details.birthdate', 'Geboortedatum')
            ->setFormType(DateType::class)
            ->setFormTypeOptions(['input'  => 'datetime_immutable'])
            ->hideOnIndex()
            ;

        yield AssociationField::new('categories', 'Categoriën')
            ->setFormTypeOptions([
                'by_reference' => false,
            ])
            ->autocomplete()
            //->renderAsNativeWidget()
            ;

        yield AssociationField::new('user')->autocomplete()->hideOnIndex();
    }
}
