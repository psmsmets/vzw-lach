<?php

namespace App\Controller\Admin;

//use App\Controller\Admin\TagCrudController;
use App\Entity\Associate;
use App\Entity\Enrolment;
use App\Entity\Event;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, Filters, KeyValueStore};
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\{AssociationField, BooleanField, ChoiceField, DateTimeField, TextField, TextareaField};
use EasyCorp\Bundle\EasyAdminBundle\Filter\{DateTimeFilter, BooleanFilter};
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;

class EnrolmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Enrolment::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
            ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDateFormat('d MMM yy')
            ->setTimeFormat('short')
            ->setDateTimeFormat('medium', 'short')
            //->setTimezone('Europe/Brussels')
            ->setNumberFormat('%.2d')
            ->setDefaultSort(['event' => 'ASC', 'associate' => 'ASC'])
            ->setPaginatorPageSize(50)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab('When');
        yield FormField::addPanel('When');

        yield AssociationField::new('event')
            ->autocomplete()
            ->setCrudController(EventCrudController::class)
            ->setFormTypeOptions([
                'by_reference' => false,
            ])
            ->setQueryBuilder(function ($queryBuilder) {
                return $queryBuilder->andWhere('entity.enrol = true'); 
            })
            ;

        yield FormField::addTab('Who');
        yield FormField::addPanel('Who');

        yield AssociationField::new('associate')
            ->autocomplete()
            ->setCrudController(AssociateCrudController::class)
            ->setFormTypeOptions([
                'by_reference' => false,
            ])
            ->setQueryBuilder(function ($queryBuilder) {
                return $queryBuilder->andWhere('entity.enabled = true'); 

            })
            ;

        yield FormField::addTab('What');
        yield FormField::addPanel('What');

        yield TextField::new('option1');
        yield TextField::new('option2');
        yield TextField::new('option3');

        yield TextareaField::new('note')->hideOnIndex();
        yield BooleanField::new('note')->renderAsSwitch(false)->onlyOnIndex();

        yield FormField::addTab('Options');
        yield FormField::addPanel('Options');

        yield BooleanField::new('cancelled')
            ->renderAsSwitch(true)
            ->onlyOnForms()
            ;
        yield BooleanField::new('cancelled')->renderAsSwitch(false)->hideOnForm();

        yield DateTimeField::new('createdAt')->onlyOnDetail();

        yield DateTimeField::new('updatedAt')->hideOnForm();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('associate')
            ->add('event')
            ->add('option1')
            ->add('option2')
            ->add('option3')
            ->add('cancelled')
        ;
    }
}
