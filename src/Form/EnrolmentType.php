<?php

namespace App\Form;

use App\Entity\Enrolment;
use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, HiddenType, TextareaType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EnrolmentType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $enrolment = $builder->getData();
        $event = $enrolment->getEvent();

        $builder
            ->add('associate', HiddenType::class, [
                'data' => $enrolment->getAssociate()->getId(),
                'mapped' => false,
            ])
        ;

        if ($event->getEnrolOptions1() != []) {
            $label = $event->getEnrolOption1();
            $builder
                ->add('option1', ChoiceType::class, [
                    'label' => (is_null($label) or $label === '') ? false : $label,
                    'placeholder' => 'Maak een keuze',
                    'choices'  => array_combine($event->getEnrolOptions1(), $event->getEnrolOptions1()),
                    'required' => true,
                    'disabled' => $event->hasEnrolUpdate() ? false : $enrolment->isEnrolled(),
                ])
                ;
        }
        if ($event->getEnrolOptions2() != []) {
            $label = $event->getEnrolOption2();
            $builder
                ->add('option2', ChoiceType::class, [
                    'label' => (is_null($label) or $label === '') ? false : $label,
                    'placeholder' => 'Maak een keuze',
                    'choices'  => array_combine($event->getEnrolOptions2(), $event->getEnrolOptions2()),
                    'required' => true,
                    'disabled' => $event->hasEnrolUpdate() ? false : $enrolment->isEnrolled(),
                ])
                ;
        }
        if ($event->getEnrolOptions3() != []) {
            $label = $event->getEnrolOption3();
            $builder
                ->add('option3', ChoiceType::class, [
                    'label' => (is_null($label) or $label === '') ? false : $label,
                    'placeholder' => 'Maak een keuze',
                    'choices'  => array_combine($event->getEnrolOptions3(), $event->getEnrolOptions3()),
                    'required' => true,
                    'disabled' => $event->hasEnrolUpdate() ? false : $enrolment->isEnrolled(),
                ])
                ;
        }
        if ($event->hasEnrolNote()) {
            $builder
                ->add('note', TextareaType::class, array(
                    'label'    => 'Heb je een vraag of wil je nog iets kwijt?',
                    'attr' => ['rows'=>4,'data-parent-class'=>'pt-4'],
                    'required' => false,
                    'disabled' => $enrolment->isEnrolled(),
                    ))
                ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Enrolment::class,
        ]);
    }
}
