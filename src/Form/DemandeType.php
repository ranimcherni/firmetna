<?php

namespace App\Form;

use App\Entity\Demande;
use App\Entity\Offre;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('offre', EntityType::class, [
                'class' => Offre::class,
                'choice_label' => 'categorie',
                'label' => 'Offre de don',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('demandeur', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Demandeur',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'En attente' => Demande::STATUT_EN_ATTENTE,
                    'Acceptée' => Demande::STATUT_ACCEPTEE,
                    'Refusée' => Demande::STATUT_REFUSEE,
                ],
                'label' => 'Statut',
                'attr' => ['class' => 'form-select'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Demande::class,
        ]);
    }
}
