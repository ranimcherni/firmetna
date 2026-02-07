<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CommandeOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité',
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'placeholder' => 'Entrez la quantité désirée',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La quantité est obligatoire.']),
                    new Assert\Positive(['message' => 'La quantité doit être au moins 1.']),
                ],
            ])
            ->add('adresseLivraison', TextareaType::class, [
                'label' => 'Adresse de livraison',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Entrez votre adresse de livraison complète',
                ],
                'constraints' => [
                    new Assert\Length(['max' => 255, 'maxMessage' => 'L\'adresse ne doit pas dépasser 255 caractères.']),
                ],
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaires (optionnel)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Remarques, préférences de livraison, etc.',
                ],
                'constraints' => [
                    new Assert\Length(['max' => 500, 'maxMessage' => 'Les commentaires ne doivent pas dépasser 500 caractères.']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
