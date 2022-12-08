<?php

namespace App\Form;

use App\Entity\AssociateDetails;
use App\Entity\AssociateAddress;
use App\Form\AssociateAddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{BirthdayType, ChoiceType, EmailType, TelType, TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociateDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('birthdate', BirthdayType::class, [
                'label' => 'Geboortedatum',
                'widget' => 'single_text',
                'required' => true,
                'input' => 'datetime_immutable',
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Geslacht',
                'placeholder' => 'Maak een keuze',
                'choices'  => [
                    'M' => 'm',
                    'V' => 'v',
                    'X' => 'x',
                ],
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'label' => 'E-mailadres van deelnemer (optioneel)',
            ])
            ->add('phone', TelType::class, [
                'required' => false,
                'label' => 'Telefoonnummer van deelnemer (optioneel)',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AssociateDetails::class,
        ]);
    }
}