<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UserType extends AbstractType
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', RepeatedType::class, array(
                'type' => EmailType::class,
                'first_options' => array(
                    'label'    => 'E-mail adres',
                    'attr' => ['placeholder'=>'e-mailadres','pattern'=>$this->params->get('app.regex.email')],
                    'required' => true,
                    ),
                'second_options' => array(
                    'label' => false,
                    'attr' => ['placeholder'=>'bevestig e-mailadres'],
                    'required' => true,
                    ),
                ))
            ->add('phone', TelType::class, [
                'required' => true,
                'label' => 'Telefoonnummer',
                'help'    => 'Enkel een Belgisch of Nederlands telefoonnummer beginnende met de landcode zonder spaties of andere tekens.',
                'attr' => ['placeholder'=>'+32...','pattern'=>$this->params->get('app.regex.phone')],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
