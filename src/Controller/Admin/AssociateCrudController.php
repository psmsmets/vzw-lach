<?php

namespace App\Controller\Admin;

use App\Entity\Associate;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AssociateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Associate::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
