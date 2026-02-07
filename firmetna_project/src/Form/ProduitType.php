<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Tomates biologiques'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 4],
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix (€)',
                'currency' => 'EUR',
                'attr' => ['class' => 'form-control'],
                'constraints' => [new NotBlank(['message' => 'Le prix est obligatoire.'])],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Produit végétal' => Produit::TYPE_VEGETALE,
                    'Produit animal' => Produit::TYPE_ANIMALE,
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('unite', ChoiceType::class, [
                'label' => 'Unité',
                'choices' => [
                    'Par kilo' => Produit::UNITE_KILO,
                    'À l\'unité' => Produit::UNITE_UNITE,
                    'Boîte' => Produit::UNITE_BOITE,
                    'Barquette' => Produit::UNITE_BARQUETTE,
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock',
                'data' => $options['data']?->getStock() ?? 0,
                'attr' => ['class' => 'form-control', 'min' => 0],
            ])
            ->add('isBio', ChoiceType::class, [
                'label' => 'Bio',
                'choices' => ['Non' => false, 'Oui' => true],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('badge', ChoiceType::class, [
                'label' => 'Badge (optionnel)',
                'required' => false,
                'choices' => [
                    '— Aucun —' => null,
                    'Bio' => 'Bio',
                    'Nouveau' => 'Nouveau',
                    'Promo' => 'Promo',
                    'Frais' => 'Frais',
                    'Fermier' => 'Fermier',
                    'Économique' => 'Économique',
                    'Productif' => 'Productif',
                    'Vivant' => 'Vivant',
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('imageUrl', TextType::class, [
                'label' => 'URL de l\'image',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'https://...'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
