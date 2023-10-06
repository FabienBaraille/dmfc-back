<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
                'required' => true, // Le champ est obligatoire
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                // en JS : event.currentTarget
                // l'entité associée au form est User
                $user = $event->getData();
                // le formulaire, pour le conditionner et parce que PHP n'a pas accès au $builder ici
                $form = $event->getForm();

                // gestion du mot de passe
                // si $user a un id, il est existant donc, edit
                if ($user->getId() !== null) {
                    // edit
                    $form->add('password', null, [
                        // on sort le mot du mapping entre le form et l'entité
                        // @see https://symfony.com/doc/5.4/reference/forms/types/text.html#mapped
                        // => le form ne tient pas compte de champ ni en lecture ni en écriture sur l'entité
                        'mapped' => false,
                        'attr' => [
                            'placeholder' => 'Laisse vide si inchangé...'
                        ]
                    ]);
                } else {
                    // add
                    $form->add('password');
                }
            })            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'required' => true,
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => [
                    'Joueur' => 'ROLE_JOUEUR',
                    'DMFC' => 'ROLE_DMFC',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
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
