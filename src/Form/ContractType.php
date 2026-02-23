<?php

namespace App\Form;

use App\Entity\Partner;
use App\Entity\Contract;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('partner', EntityType::class, [
                'class' => Partner::class,
                'choice_label' => 'name',
                'label' => 'Partenaire',
                'attr' => ['class' => 'form-select'],
                'placeholder' => '-- Choisir un partenaire --',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de contrat',
                'choices' => [
                    'Sponsorship' => 'Sponsorship',
                    'Product' => 'Product',
                    'Service' => 'Service',
                    'Other' => 'Other',
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Don de semences'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 4],
            ])
            ->add('amount', TextType::class, [
                'label' => 'Montant (optionnel)',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: 1500.00'],
            ])
            ->add('dateDebutContract', DateType::class, [
                'label' => 'Date de début du contrat',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('dateFinContract', DateType::class, [
                'label' => 'Date de fin du contrat',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('offerDate', DateType::class, [
                'label' => 'Date du contrat',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Actif' => 'Actif',
                    'Suspendu' => 'Suspendu',
                    'Terminé' => 'Terminé',
                    'Annulé' => 'Annulé',
                ],
                'attr' => ['class' => 'form-select'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contract::class,
        ]);
    }
}
