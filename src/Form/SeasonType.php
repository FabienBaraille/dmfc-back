<?php

namespace App\Form;

use App\Entity\Season;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SeasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('year', TextType::class, [
                'label' => "Année de la saison"
            ])
            ->add('startSeason', DateType::class, [
                'label' => "Date de début de la saison",
                'widget' => 'single_text',
            ])
            ->add('startPlayoff', DateType::class, [
                'label' => "Date de début des playoffs",
                'widget' => 'single_text',
            ])
            ->add('Comment', TextType::class, [
                'label' => "Commentaire",
            ]);

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Season::class,
        ]);
    }
}
