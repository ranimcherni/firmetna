<?php

namespace App\Form;

use App\Entity\Commentaire;
use App\Entity\Publication;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu', TextareaType::class, [
                'label' => 'Commentaire',
                'attr' => ['placeholder' => 'Écrivez un commentaire...', 'rows' => 3, 'class' => 'form-control'],
            ]);

        if ($options['is_admin']) {
            $builder
                ->add('auteur', EntityType::class, [
                    'class' => \App\Entity\User::class,
                    'choice_label' => 'email',
                    'label' => 'Auteur',
                    'attr' => ['class' => 'form-control select2'],
                ])
                ->add('publication', EntityType::class, [
                    'class' => Publication::class,
                    'choice_label' => 'titre',
                    'label' => 'Publication',
                    'attr' => ['class' => 'form-control select2'],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
            'is_admin' => false,
        ]);
    }
}
