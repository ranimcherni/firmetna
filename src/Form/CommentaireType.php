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
            ->add('publication', EntityType::class, [
                'class' => Publication::class,
                'choice_label' => 'titre',
                'label' => 'Publication associée',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('auteur', EntityType::class, [
                'class' => \App\Entity\User::class,
                'choice_label' => 'email',
                'label' => 'Auteur',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu du commentaire',
                'attr' => ['placeholder' => 'Répondez ou partagez votre avis...', 'rows' => 5, 'class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
        ]);
    }
}
