<?php

namespace App\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\AssociateDetails;

class GenderFilterType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => AssociateDetails::GENDERS
        ]);
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }
}
