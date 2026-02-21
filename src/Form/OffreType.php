<?php

namespace App\Form;

use App\Entity\Offre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class OffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Votre nom',
                'attr' => ['placeholder' => 'Ex: Ben Ali'],
                'constraints' => [new NotBlank(['message' => 'Le nom est obligatoire.'])],
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => ['placeholder' => 'Ex: +216 12 345 678'],
                'constraints' => [new NotBlank(['message' => 'Le téléphone est obligatoire.'])],
            ])
            ->add('categorie', ChoiceType::class, [
                'label' => 'Catégorie du produit',
                'placeholder' => 'Choisir une catégorie',
                'choices' => [
                    'Tracteur / Machinisme' => 'Tracteur',
                    'Engrais' => 'Engrais',
                    'Légumes' => 'Légumes',
                    'Fruits' => 'Fruits',
                    'Semences' => 'Semences',
                    'Outillage' => 'Outillage',
                    'Autre' => 'Autre',
                ],
                'constraints' => [new NotBlank(['message' => 'La catégorie est obligatoire.'])],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 4, 'placeholder' => 'Décrivez le produit ou l\'offre...'],
                'constraints' => [new NotBlank(['message' => 'La description est obligatoire.'])],
            ])
            ->add('quantite', TextType::class, [
                'label' => 'Quantité ou précision (optionnel)',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: 50 kg, 1 unité, 10 sacs'],
            ])
            ->add('photoFile', FileType::class, [
                'label' => 'Photo du produit',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '3M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Veuillez uploader une image (JPG, PNG ou WEBP).',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offre::class,
        ]);
    }
}
