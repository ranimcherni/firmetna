<?php

namespace App\Form;

use App\Entity\Partner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du partenaire',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Coopérative Agricole XYZ'],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Entreprise' => 'Entreprise',
                    'ONG' => 'ONG',
                    'Coopérative' => 'Coopérative',
                    'Association' => 'Association',
                    'Institution' => 'Institution',
                    'Autre' => 'Autre',
                ],
                'placeholder' => '-- Choisir --',
                'required' => false,
                'attr' => ['class' => 'form-select'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 4],
            ])
            ->add('website', UrlType::class, [
                'label' => 'Site web',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'https://...'],
            ])
            ->add('logoUrl', TextType::class, [
                'label' => 'URL du logo',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'https://... ou /uploads/...'],
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Actif' => 'Actif',
                    'Inactif' => 'Inactif',
                    'En attente' => 'En attente',
                ],
                'attr' => ['class' => 'form-select'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Partner::class,
        ]);
    }
}
