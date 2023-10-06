<?php

namespace App\Form;

use App\Entity\Team;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('trigram')
            ->add('name', TextType::class, [
                'label' => "Nom de l'équipe NBA"
            ])
            ->add('conference', ChoiceType::class, [
                'label' => 'Conférence',
                'choices' => [
                    'Eastern' => "Eastern",
                    'Western' => "Western"
                ],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('logo')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
