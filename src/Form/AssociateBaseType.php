<?php

namespace App\Form;

use App\Entity\Associate;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AssociateBaseType extends AbstractType
{
    const PREF_ACTEUR = 'acteur';
    const PREF_FIGURANT = 'figurant';
    const PREF_PITCHOIR = 'pitchoir';
    const PREF_ORKEST = 'orkest';
    const PREF_BACKSTAGE = 'backstage-medewerker';
    const PREF_DECOR = 'decormedewerker';
    const PREF_KOSTUUM = 'kostuummedewerker';
    const PREF_PRODUCTIE = 'productieteam';
    const PREF_ARTISTIEK = 'artistiek team';

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'required' => true,
                'label' => 'Voornaam',
                'attr' => ['pattern' => $this->params->get('app.regex.name')],
            ])
            ->add('lastname', TextType::class, [
                'required' => true,
                'label' => 'Familienaam',
                'attr' => ['pattern' => $this->params->get('app.regex.name')],
            ])
            ->add('categoryPreferences', ChoiceType::class, [
                'required' => true,
                'label' => 'Wat wil je graag doen? Geef je voorkeur door.',
                'choices' => [
                    'Acteur' => self::PREF_ACTEUR,
                    'Figurant' => self::PREF_FIGURANT,
                    'Pitchoir' => self::PREF_PITCHOIR,
                    'Orkest' => self::PREF_ORKEST,
                    'Backstage-medewerker' => self::PREF_BACKSTAGE,
                    'Decormedewerker' => self::PREF_DECOR,
                    'Kostuummedewerker' => self::PREF_KOSTUUM,
                    // 'Productieteam' => self::PREF_PRODUCTIE, // protected
                    'Artistiek team' => self::PREF_ARTISTIEK,
                ],
                'help' => 'Je kan meerdere opties aanduiden.',
                'label_attr' => [
                    'class' => 'checkbox-switch',
                ],
                'expanded'  => true,
                'multiple'  => true,
            ])
            ->add('singer', CheckboxType::class, [
                'required' => false,
                'label' => 'Ik heb ervaring met zingen',
                'label_attr' => [
                    'class' => 'checkbox-switch',
                ],
                'row_attr' => [
                    'class' => 'acteur-figurant mb-1 mt-3',
                ],
            ])
            ->add('singerSoloist', CheckboxType::class, [
                'required' => false,
                'label' => 'Ik heb ervaring met alleen zingen, ik zou wel een solistenrol aankunnen',
                'label_attr' => [
                    'class' => 'checkbox-switch',
                ],
                'row_attr' => [
                    'class' => 'acteur-figurant mb-3',
                ],
            ])
            ->add('companion', TextType::class, [
                'required' => false,
                'label' => 'Ik zou graag in dezelfde scènes zitten als',
                'attr' => [
                    'placeholder' => 'Geef één of meerdere namen in',
                ],
                'row_attr' => [
                    'class' => 'acteur-figurant mb-3',
                ],
            ])
            ->add('imagePortraitFile', VichImageType::class, [
                'required' => false,
                'label' => 'Een portretfoto van jezelf.',
                'allow_delete' => true,
                'asset_helper' => true,
                'row_attr' => [
                    'class' => 'acteur-figurant mb-3',
                ],
                'help' => 'Max 8MB. Een te grote foto? Verklein deze dan eerst met <a href="https://imresizer.com/resize-image-to-2mb" target=_Blank>imresizer.com/resize-image-to-2mb</a>',
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
                'help' => 'Max 8MB. Een te grote foto? Verklein deze dan eerst met <a href="https://imresizer.com/resize-image-to-2mb" target=_Blank>imresizer.com/resize-image-to-2mb</a>',
                'help_html' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
//            'data_class' => Associate::class,
        ]);
    }
}
