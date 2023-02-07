<?php

namespace App\Form;

use App\Entity\Associate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

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
            ->add('details', AssociateDetailsType::class, [
                'row_attr' => [
                    'class' => 'mt-3',
                ],
                'label' => false,
            ])
            ->add('address', AssociateAddressType::class, [
                'row_attr' => [
                    'class' => 'mt-3',
                ],
                'label' => 'Adres',
            ])
            ->add('imagePortraitFile', VichImageType::class, [
                'required' => false,
                'label' => 'Een portretfoto van jezelf.',
                'download_uri' => false,
                'allow_delete' => true,
                'asset_helper' => false,
                'attr' => [
                    'accept' => 'image/jpeg',
                ],
                'row_attr' => [
                    'class' => 'acteur-figurant my-3',
                ],
                'help' => 'Enkel JPG en maximaal 10MB. Een te grote foto? Verklein deze dan eerst met <a href="https://imresizer.com/resize-image-to-2mb" target=_Blank>imresizer.com/resize-image-to-2mb</a>',
                'help_html' => true,
            ])
            ->add('imageEntireFile', VichImageType::class, [
                'required' => false,
                'label' => 'Een volledige foto van kop tot teen.',
                'download_uri' => false,
                'allow_delete' => true,
                'asset_helper' => true,
                'attr' => [
                    'accept' => 'image/jpeg',
                ],
                'row_attr' => [
                    'class' => 'acteur-figurant my-3',
                ],
                'help' => 'Enkel JPG en maximaal 10MB. Een te grote foto? Verklein deze dan eerst met <a href="https://imresizer.com/resize-image-to-2mb" target=_Blank>imresizer.com/resize-image-to-2mb</a>',
                'help_html' => true,
            ])
        ;
        if ($builder->getData()->isOnstage()) {
            $builder->add('measurements', AssociateMeasurementsType::class, [
                'row_attr' => [
                    'class' => 'mt-3',
                ],
                'label' => 'Uiterlijk en kledingmaat',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Associate::class,
            'csrf_protection' => false,
        ]);
    }
}
