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
use Symfony\Component\Validator\Constraints\Regex;

class OffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('telephone', TextType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => ['placeholder' => 'Ex: 12345678'],
                'constraints' => [
                    new NotBlank(['message' => 'Le téléphone est obligatoire.']),
                    new Regex([
                        'pattern' => '/^\d{8}$/',
                        'message' => 'Le numéro doit contenir exactement 8 chiffres.',
                    ]),
                ],
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
                'constraints' => [
                    new NotBlank(['message' => 'La catégorie est obligatoire.']),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 4, 'placeholder' => 'Décrivez le produit ou l\'offre...'],
                'constraints' => [
                    new NotBlank(['message' => 'La description est obligatoire.']),
                ],
            ])
            ->add('quantite', \Symfony\Component\Form\Extension\Core\Type\IntegerType::class, [
                'label' => 'Quantité disponible',
                'required' => true,
                'attr' => ['placeholder' => 'Ex: 50'],
                'constraints' => [
                    new NotBlank(['message' => 'La quantité est obligatoire.']),
                ],
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