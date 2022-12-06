<?php

namespace App\Form;

use App\Entity\Associate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociateBaseType extends AbstractType
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Associate::class,
        ]);
    }
}
