<?php

namespace App\Form;

use App\Entity\AssociateMeasurements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociateMeasurementsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('hairColor', ChoiceType::class, [
                'label' => 'Haarkleur',
                'placeholder' => 'Maak een keuze',
                'choices'  => AssociateMeasurements::HAIRCOLORS,
                'required' => false,
            ])
            ->add('hairType', ChoiceType::class, [
                'label' => 'Haartype',
                'placeholder' => 'Maak een keuze',
                'choices'  => AssociateMeasurements::HAIRTYPES,
                'required' => false,
            ])
            ->add('hairLength', ChoiceType::class, [
                'label' => 'Haarlengte',
                'placeholder' => 'Maak een keuze',
                'choices'  => AssociateMeasurements::HAIRLENGTHS,
                'required' => false,
            ])
            ->add('fittingSize', IntegerType::class, [
                'label' => 'Confectiemaat',
                'required' => false,
            ])
            ->add('height', IntegerType::class, [
                'label' => 'Lengte in cm',
                'required' => false,
            ])
            ->add('chestGirth', IntegerType::class, [
                'label' => 'Borstomvang in cm',
                'required' => false,
            ])
            ->add('waistGirth', IntegerType::class, [
                'label' => 'Taille in cm',
                'required' => false,
            ])
            ->add('hipGirth', IntegerType::class, [
                'label' => 'Heupomtrek in cm',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AssociateMeasurements::class,
            'csrf_protection' => false,
        ]);
    }
}
