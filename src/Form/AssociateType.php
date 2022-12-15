<?php

namespace App\Form;

use App\Entity\Associate;
use App\Entity\AssociateAddress;
use App\Entity\AssociateDetails;
use App\Form\AssociateDetailsType;
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
            ->add('details', AssociateDetailsType::class)
            ->add('address', AssociateAddressType::class)
            ->add('imagePortraitFile', VichImageType::class, [
                'required' => false,
                'label' => 'Een portretfoto van jezelf.',
                'allow_delete' => true,
                'asset_helper' => false,
                'row_attr' => [
                    'class' => 'acteur-figurant mb-3',
                ],
                'help' => 'Enkel JPG en maximaal 10MB. Een te grote foto? Verklein deze dan eerst met <a href="https://imresizer.com/resize-image-to-2mb" target=_Blank>imresizer.com/resize-image-to-2mb</a>',
                'help_html' => true,
            ])
            ->add('imageEntireFile', VichImageType::class, [
                'required' => false,
                'label' => 'Een volledige foto van kop tot teen.',
                'allow_delete' => true,
                'asset_helper' => true,
                'row_attr' => [
                    'class' => 'acteur-figurant mb-3',
                ],
                'help' => 'Enkel JPG en maximaal 10MB. Een te grote foto? Verklein deze dan eerst met <a href="https://imresizer.com/resize-image-to-2mb" target=_Blank>imresizer.com/resize-image-to-2mb</a>',
                'help_html' => true,
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
