<?php

namespace App\Form;

use App\Entity\Associate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociateDeclarationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('declarePresent', CheckboxType::class, [
                'label' => 'Ik verklaar dat ik in principe 100% aanwezig zal zijn op de momenten dat ik verwacht word (repetities en show)',
                'required' => true,
            ])
            ->add('declareSecrecy', CheckboxType::class, [
                'label' => 'Ik verklaar geheimhouding over de inhoud van de voorstelling',
                'required' => true,
            ])
            ->add('declareTerms', CheckboxType::class, [
                'label' => 'Ik verklaar kennis genomen te hebben van de verzekerde risico’s binnen de vrijwilligerswerking van vzw LA:CH',
                'required' => true,
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
