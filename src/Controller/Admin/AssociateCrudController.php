<?php

namespace App\Controller\Admin;

use App\Controller\Admin\CategoryCrudController;
use App\Controller\Admin\Filter\{AssociationDateTimeFilter, AssociationNumericFilter, AssociationTextFilter, GenderFilter};
use App\Controller\Admin\UserCrudController;
use App\Entity\Associate;
use App\Entity\AssociateDetails;
use App\Entity\AssociateMeasurements;
use App\Form\AssociateBaseType;
use App\Service\AssociateExport;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, Filters, KeyValueStore};
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\{BatchActionDto, EntityDto};
use EasyCorp\Bundle\EasyAdminBundle\Field\{Field, AssociationField, BooleanField, ChoiceField, DateField, ImageField, NumberField, TextField, EmailField, TelephoneField};
use EasyCorp\Bundle\EasyAdminBundle\Filter\{ArrayFilter};
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AssociateCrudController extends AbstractCrudController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator,
        private AssociateExport $export,
    )
    {}

    public static function getEntityFqcn(): string
    {
        return Associate::class;
    }

    public function configureActions(Actions $actions): Actions
    {

        $exportBdays = Action::new('exportBdays', 'Exporteer verjaardagen')
            ->setIcon('bi bi-person-lines-fill')
            ->addCssClass('btn btn-primary')
            ->linkToRoute('api_export_associate_birthdays')
            ->createAsGlobalAction()
            ;

        $exportDetails = Action::new('exportDetails', 'Exporteer ledendetails')
            ->setIcon('bi bi-person-fill-lock')
            ->addCssClass('btn btn-primary')
            ->linkToRoute('api_export_associate_details')
            ->createAsGlobalAction()
            ;

        return $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $exportBdays)
            ->add(Crud::PAGE_INDEX, $exportDetails)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->addBatchAction(Action::new('batchExportBdays', 'Exporteer verjaardagen')
                ->linkToCrudAction('batchExportBdays')
                ->addCssClass('btn btn-primary')
                ->setIcon('bi bi-person-lines-fill')
                )
            ->addBatchAction(Action::new('batchExportDetails', 'Exporteer ledendetails')
                ->linkToCrudAction('exportDetails')
                ->addCssClass('btn btn-primary')
                ->setIcon('bi bi-person-fill-lock')
                )
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

        yield EmailField::new('users.email')
            ->formatValue(function ($value, $entity) {
                $users = [];
                foreach ($entity->getUsers() as $user) {
                    $url = $this->adminUrlGenerator
                        ->setController(UserCrudController::class)
                        ->setAction(Action::DETAIL)
                        ->setEntityId($user->getId())
                        ->generateUrl()
                        ;
                    $users[] = sprintf("<a class=\"btn btn-sm btn-primary me-1\" href=\"%s\">%s</a>", $url, $user->getEmail());
                }
                return implode("\t", $users);
            })
            ->setTemplatePath('admin/field/association.html.twig')
            ->onlyOnDetail()
            ;
        yield TelephoneField::new('users.phone')
            ->formatValue(function ($value, $entity) {
                $users = [];
                foreach ($entity->getUsers() as $user) {
                    if (($phone = $user->getPhone())) {
                        $url = $this->adminUrlGenerator
                            ->setController(UserCrudController::class)
                            ->setAction(Action::DETAIL)
                            ->setEntityId($user->getId())
                            ->generateUrl()
                            ;
                        $users[] = sprintf("<a class=\"btn btn-sm btn-primary me-1\" href=\"%s\">%s</a>", $url, $phone);
                    }
                }
                return implode("\t", $users);
            })
            ->setTemplatePath('admin/field/association.html.twig')
            ->onlyOnDetail()
            ;

        yield EmailField::new('details.email')->hideOnIndex();
        yield TelephoneField::new('details.phone')->hideOnIndex();
        yield TextField::new('address.address')->onlyOnDetail();

        yield FormField::addTab('Categories');
        yield FormField::addPanel('Categories');

        yield AssociationField::new('categories', 'Groep(en)')
            ->autocomplete()
            ->setCrudController(CategoryCrudController::class)
            ->setFormTypeOptions([
                'by_reference' => false,
            ])
            ->setQueryBuilder(function ($queryBuilder) {
                return $queryBuilder->andWhere('entity.enabled = true'); 

            })
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
        yield ChoiceField::new('measurements.hairColor', 'Haarkleur')
            ->setChoices(AssociateMeasurements::HAIRCOLORS)
            ->hideOnIndex()
            ;
        yield ChoiceField::new('measurements.hairType', 'Haartype')
            ->setChoices(AssociateMeasurements::HAIRTYPES)
            ->hideOnIndex()
            ;
        yield ChoiceField::new('measurements.hairLength', 'Haarlengte')
            ->setChoices(AssociateMeasurements::HAIRLENGTHS)
            ->hideOnIndex()
            ;
        yield Field::new('measurements.fittingSize', 'Confectiemaat')->hideOnIndex();
        yield Field::new('measurements.height', 'Lengte in cm')->hideOnIndex();
        yield Field::new('measurements.chestGirth', 'Borstomvang in cm')->hideOnIndex();
        yield Field::new('measurements.waistGirth', 'Taille in cm')->hideOnIndex();
        yield Field::new('measurements.hipGirth', 'Heup in cm')->hideOnIndex();

        yield FormField::addTab('Options');

       yield AssociationField::new('users')->onlyOnIndex();

        yield BooleanField::new('enabled')->renderAsSwitch(false)->onlyOnDetail();
        yield BooleanField::new('enabled')->renderAsSwitch(true)->onlyOnForms();
        yield Field::new('id')->onlyOnDetail();
        yield Field::new('createdAt')->hideOnForm();
        yield BooleanField::new('declarePresent', 'Akkoord aanwezig')->hideOnIndex();
        yield BooleanField::new('declareSecrecy', 'Akkoord geheimhouding')->hideOnIndex();
        yield BooleanField::new('declareTerms', 'Akkoord voorwaarden')->hideOnIndex();
        yield Field::new('updatedAt')->hideOnForm();
/*
        yield AssociationField::new('user')
            ->autocomplete()
            ->setCrudController(UserCrudController::class)
            ->setQueryBuilder(function ($queryBuilder) {
                return $queryBuilder->andWhere('entity.enabled = true'); // your query

            })
            ->hideOnIndex()
            ;
*/
        yield AssociationField::new('users')
            ->autocomplete()
            ->setCrudController(UserCrudController::class)
            //->setQueryBuilder(function ($queryBuilder) {
            //    return $queryBuilder->andWhere('entity.enabled = true'); // your query
            //})
            ->onlyOnForms()
            ;
        yield AssociationField::new('users')
            // https://stackoverflow.com/questions/72350335/render-as-multiple-bagdes-with-an-associationfield-in-easyadmin
            ->formatValue(function ($value, $entity) {
                $users = [];
                foreach ($entity->getUsers() as $user) {
                    $url = $this->adminUrlGenerator
                        ->setController(UserCrudController::class)
                        ->setAction(Action::DETAIL)
                        ->setEntityId($user->getId())
                        ->generateUrl()
                        ;
                    $users[] = sprintf("<a class=\"btn btn-sm btn-primary me-1\" href=\"%s\">%s</a>", $url, $user);
                }
                return implode("\t", $users);
            })
            ->setTemplatePath('admin/field/association.html.twig')
            ->onlyOnDetail()
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
            ->add(
                ArrayFilter::new('categoryPreferences')
                ->setChoices(AssociateBaseType::PREF_CATEGORIES)
                ->setFormTypeOption('mapped', false)
            )
            ->add('categories', 'Toegewezen groep')
        ;
    }

    public function batchExportBdays(BatchActionDto $batchActionDto)
    {
        $className = $batchActionDto->getEntityFqcn();
        $entityManager = $this->container->get('doctrine')->getManagerForClass($className);

        $associates = [];

        foreach ($batchActionDto->getEntityIds() as $id) {
            $associate = $entityManager->find($className, $id);

            if (is_null($associate->getDetails()->getBirthdate()) or !$associate->isEnabled()) continue;

            $associates[] = $associate;
        }

        return $this->export->exportBdays($associates);
    }

    public function exportDetails(BatchActionDto $batchActionDto)
    {
        $user = $this->security->getUser();
        $this->logger->info(sprintf("Admin %s (%s) requested to export associate details.", $user, $user->getEmail()));

        $className = $batchActionDto->getEntityFqcn();
        $entityManager = $this->container->get('doctrine')->getManagerForClass($className);

        $headers = ['naam', 'voornaam', 'adres', 'geboortedatum', 'functieomschrijving', 'groep(en)'];
        $datas = [];

        foreach ($batchActionDto->getEntityIds() as $id) {
            $associate = $entityManager->find($className, $id);

            if (!$associate->isEnabled() or count($associate->getCategories()) == 0) continue;

            $data = [
                $associate->getLastname(),
                $associate->getFirstname(),
                $associate->getAddress()->getAddress(),
                $associate->getDetails()->getBirthdate()->format('Y-m-d'),
                $associate->isOnstage() ? 'acteur/figurant' : 'vrijwilliger',
                $associate->getCategoryNames(),
            ];

            $datas[] = $data;
        }

        asort($datas);

        return $this->spreadsheet->export('HGCVHKV ledendetails', $datas, $headers);
    }
}
