<?php

namespace App\Form;

use App\Entity\AssociateAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociateAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('line1', TextType::class, [
                'label' => 'Straat + nummer',
                'required' => true,
            ])
/*
            ->add('line2', TextType::class, [
                'label' => 'Adresregel 2',
                'required' => false,
            ])
*/
            ->add('zip', TextType::class, [
                'label' => 'Postcode',
                'required' => true,
            ])
            ->add('town', TextType::class, [
                'label' => 'Gemeente',
                'required' => true,
            ])
            ->add('nation', CountryType::class, [
                'label' => 'Land',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AssociateAddress::class,
        ]);
    }
}
