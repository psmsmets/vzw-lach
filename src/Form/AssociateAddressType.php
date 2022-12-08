<?php

namespace App\Form;

use App\Entity\AssociateAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AssociateAddressType extends AbstractType
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('line1', TextType::class, [
                'label' => 'Straat + nummer',
                'required' => true,
                'attr' => ['pattern' => $this->params->get('app.regex.streetnr')],
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
                'attr' => ['pattern' => $this->params->get('app.regex.zip')],
            ])
            ->add('town', TextType::class, [
                'label' => 'Gemeente',
                'required' => true,
                'attr' => ['pattern' => $this->params->get('app.regex.name')],
            ])
            ->add('nation', CountryType::class, [
                'label' => 'Land',
                'preferred_choices' => ['BE','NL'],
                'required' => true,
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
