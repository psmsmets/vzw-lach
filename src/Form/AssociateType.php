<?php

namespace App\Form;

use App\Entity\Associate;
use App\Entity\AssociateDetails;
use App\Form\AssociateDetailsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'required' => true,
                'label' => 'Voornaam',
            ])
            ->add('lastname', TextType::class, [
                'required' => true,
                'label' => 'Familienaam',
            ])
            ->add('singer', CheckboxType::class, [
                'required' => false,
                'label' => 'Ik heb ervaring met zingen',
                'label_attr' => [
                    'class' => 'checkbox-switch',
                ],
            ])
            ->add('singerSoloist', CheckboxType::class, [
                'required' => false,
                'label' => 'Ik heb ervaring met alleen zingen, ik zou wel een solistenrol aankunnen',
                'label_attr' => [
                    'class' => 'checkbox-switch',
                ],
            ])
            ->add('companion', TextType::class, [
                'required' => false,
                'label' => 'Ik zou graag in dezelfde scènes zitten als',
            ])
            ->add('details', AssociateDetailsType::class)
            ->add('declarePresent', CheckboxType::class, [
                'required' => true,
                'label' => 'Ik verklaar dat ik in principe 100% aanwezig zal zijn op de momenten dat ik verwacht word (repetities en show)',
            ])
            ->add('declareSecrecy', CheckboxType::class, [
                'required' => true,
                'label' => 'Ik verklaar geheimhouding over de inhoud van de voorstelling',
            ])
            ->add('declareRisks', CheckboxType::class, [
                'required' => true,
                'label' => 'Ik verklaar kennis genomen te hebben van de verzekerde risico’s binnen de vrijwilligerswerking van vzw LA:CH',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Associate::class,
        ]);
    }
}
