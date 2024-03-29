<?php

namespace App\Controller\Admin;

//use App\Controller\Admin\TagCrudController;
use App\Entity\Event;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, Filters, KeyValueStore};
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\{ArrayField, AssociationField, IdField, BooleanField, DateTimeField, IntegerField, MoneyField, TextField, TextareaField, TextEditorField};
use EasyCorp\Bundle\EasyAdminBundle\Filter\{DateTimeFilter, BooleanFilter};
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters
    ): QueryBuilder
    {
        $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $qb->andWhere('entity.startTime >= :ref');
        $qb->setParameter('ref', (new \DateTimeImmutable('today midnight'))->modify('-3 days'));

        return $qb;
    }

    public function configureActions(Actions $actions): Actions
    {

        $exportEnrolments = Action::new('exportEnrolments', 'Export Enrolments')
            ->setIcon('bi bi-person-lines-fill')
            ->linkToRoute('api_export_event_enrolments', function (Event $event): array {
                return [
                    'id' => $event->getId(),
                ];
            })
            //->createAsGlobalAction()
            ;

        return $actions
            ->add(Crud::PAGE_DETAIL, $exportEnrolments)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
            ->setPermission(Action::NEW, 'ROLE_ADMIN')
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
            ->setDefaultSort(['startTime' => 'ASC'])
            ->setPaginatorPageSize(50)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab('What');
        yield FormField::addPanel('What');

        yield IdField::new('id')->onlyOnDetail();

        yield TextField::new('title');

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
        //yield TextField::new('description')->hideOnIndex();
        yield TextField::new('location')->hideOnIndex();
        yield TextField::new('url')->hideOnIndex();

        yield FormField::addTab('When');
        yield FormField::addPanel('When');

        yield DateTimeField::new('startTime');
        yield DateTimeField::new('endTime');
        yield BooleanField::new('nullifyEndTime', 'Geen einduur (tot het einde van de dag)')
            ->renderAsSwitch(true)
            ->onlyOnForms()
            ;

        yield BooleanField::new('allDay', 'Hele dag (geen begin en einduur)')
            ->renderAsSwitch(true)
            ->onlyOnForms()
            ;
        yield BooleanField::new('allDay')
            ->renderAsSwitch(false)
            ->hideOnForm()
            ;

        yield FormField::addTab('Who');
        yield FormField::addPanel('Who');

        yield AssociationField::new('categories')
            ->autocomplete()
            ->setFormTypeOptions([
                'by_reference' => false,
            ])
            ->hideOnIndex()
            ;
        yield AssociationField::new('tags')
            ->autocomplete()
            ->setFormTypeOptions([
                'by_reference' => false,
            ])
            ->hideOnIndex()
            ;

        yield TextField::new('location')->onlyOnIndex();

        yield FormField::addTab('Inschrijven');
        yield FormField::addPanel('Inschrijven');

        yield BooleanField::new('enrol')
            ->renderAsSwitch(true)
            ->onlyOnForms()
            ->setHelp('Opgelet: dit kan enkel aangepast worden voor publicatie!')
            ;
        yield BooleanField::new('enrol')->renderAsSwitch(false)->hideOnForm();

        yield IntegerField::new('enrolBeforeDays')->hideOnIndex();
        yield DateTimeField::new('enrolBefore')->onlyOnDetail();

        yield MoneyField::new('enrolCharge')->setCurrency('EUR')->hideOnIndex();
        yield BooleanField::new('enrolFreeOfCharge')->renderAsSwitch(true)->onlyOnForms();
        yield BooleanField::new('enrolFreeOfCharge')->renderAsSwitch(false)->onlyOnDetail();

        yield BooleanField::new('enrolMaybe')->renderAsSwitch(true)->onlyOnForms();
        yield BooleanField::new('enrolMaybe')->renderAsSwitch(false)->onlyOnDetail();

        yield BooleanField::new('enrolUpdate')->renderAsSwitch(true)->onlyOnForms();
        yield BooleanField::new('enrolUpdate')->renderAsSwitch(false)->onlyOnDetail();

        yield BooleanField::new('enrolNote')->renderAsSwitch(true)->onlyOnForms();
        yield BooleanField::new('enrolNote')->renderAsSwitch(false)->onlyOnDetail();

        yield TextField::new('enrolOption1')
            ->hideOnIndex()
            ->setHelp('Laat leeg om geen omschrijving te tonen voor keuzelijst 1 op het inschrijfformulier')
            ;
        yield ArrayField::new('enrolOptions1')
            ->hideOnIndex()
            ->setHelp('Laat leeg om geen keuzelijst 1 te tonen op het inschrijfformulier')
            ;

        yield TextField::new('enrolOption2')
            ->hideOnIndex()
            ->setHelp('Laat leeg om geen omschrijving te tonen voor keuzelijst 2 op het inschrijfformulier')
            ;
        yield ArrayField::new('enrolOptions2')
            ->hideOnIndex()
            ->setHelp('Laat leeg om geen keuzelijst 2 te tonen op het inschrijfformulier')
            ;

        yield TextField::new('enrolOption3')
            ->hideOnIndex()
            ->setHelp('Laat leeg om geen omschrijving te tonen voor keuzelijst 3 op het inschrijfformulier')
            ;
        yield ArrayField::new('enrolOptions3')
            ->hideOnIndex()
            ->setHelp('Laat leeg om geen keuzelijst 3 te tonen op het inschrijfformulier')
            ;

        yield AssociationField::new('enrolments')->hideOnForm();

        yield FormField::addTab('Options');
        yield FormField::addPanel('Options');

        yield BooleanField::new('overruled')
            ->renderAsSwitch(true)
            ->onlyOnForms()
            ->setHelp('Een SUPER ADMIN kan onderstaande opties overrulen via deze switch!')
            ->setPermission('ROLE_SUPER_ADMIN')
            ;

        yield DateTimeField::new('publishedAt')->setRequired(false);
        yield BooleanField::new('published')
            ->renderAsSwitch(true)
            ->onlyOnForms()
            ->hideWhenCreating()
            ->setHelp('Opgelet: dit kan niet ongedaan worden!')
            ;
        yield BooleanField::new('published')->renderAsSwitch(false)->hideOnForm();

        yield BooleanField::new('cancelled')
            ->renderAsSwitch(true)
            ->onlyOnForms()
            ->setHelp('Opgelet: dit kan niet ongedaan worden!')
            ;
        yield BooleanField::new('cancelled')->renderAsSwitch(false)->hideOnForm();

        yield DateTimeField::new('createdAt')->onlyOnDetail();

        yield DateTimeField::new('updatedAt')->hideOnForm();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('published')
            ->add(DateTimeFilter::new('startTime'))
            ->add('endTime')
            ->add('allDay')
            ->add('cancelled')
            ->add('enrol')
        ;
    }
}
