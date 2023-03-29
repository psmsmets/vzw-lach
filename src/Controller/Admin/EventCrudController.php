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
use EasyCorp\Bundle\EasyAdminBundle\Field\{AssociationField, IdField, BooleanField, DateTimeField, TextField, TextareaField, TextEditorField};
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
        return $actions
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

        yield AssociationField::new('categories')->autocomplete()->hideOnIndex();
        yield AssociationField::new('tags')->autocomplete()->hideOnIndex();

        yield TextField::new('location')->onlyOnIndex();
        
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
//dd(DateTimeFilter::new('startTime'));
        return $filters
            ->add('published')
            ->add(DateTimeFilter::new('startTime'))
            ->add('endTime')
            ->add('allDay')
            ->add('cancelled')
        ;
    }
}
