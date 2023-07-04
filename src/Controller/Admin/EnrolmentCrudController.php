<?php

namespace App\Controller\Admin;

use App\Entity\Associate;
use App\Entity\Enrolment;
use App\Entity\Event;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, Filters, KeyValueStore};
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\{BatchActionDto, EntityDto, SearchDto};
use EasyCorp\Bundle\EasyAdminBundle\Field\{AssociationField, BooleanField, ChoiceField, DateTimeField, MoneyField, TextField, TextareaField};
use EasyCorp\Bundle\EasyAdminBundle\Filter\{DateTimeFilter, BooleanFilter};
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;

class EnrolmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Enrolment::class;
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters
    ): QueryBuilder
    {
        $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $qb->andWhere('event.startTime >= :ref');
        $qb->orWhere('entity.paid = false');
        $qb->setParameter('ref', (new \DateTimeImmutable('today midnight'))->modify('+3 days'));

        return $qb;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
            ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->addBatchAction(Action::new('paid', 'Mark Paid')
                ->linkToCrudAction('markPaid')
                ->addCssClass('btn btn-primary')
                ->setIcon('bi bi-person-check'))
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
            ->setDefaultSort(['event' => 'ASC', 'associate.firstname' => 'ASC', 'associate.lastname' => 'ASC'])
            ->setPaginatorPageSize(50)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab('When');
        yield FormField::addPanel('When');

        yield AssociationField::new('event')
            ->hideWhenUpdating()
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
            ->hideWhenUpdating()
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

        yield MoneyField::new('totalCharge')->setCurrency('EUR');

        yield BooleanField::new('paid')
            ->renderAsSwitch(true)
            ->onlyOnForms()
            ;
        yield BooleanField::new('paid')->renderAsSwitch(false)->hideOnForm();

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
            ->add('paid')
        ;
    }

    public function markPaid(BatchActionDto $batchActionDto)
    {
        $className = $batchActionDto->getEntityFqcn();
        $entityManager = $this->container->get('doctrine')->getManagerForClass($className);

        foreach ($batchActionDto->getEntityIds() as $id) {
            $enrolment = $entityManager->find($className, $id);
            $enrolment->setPaid(true);
        }

        $entityManager->flush();

        return $this->redirect($batchActionDto->getReferrerUrl());
    }
}
