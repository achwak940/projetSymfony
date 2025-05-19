<?php

namespace App\Form;
use App\Entity\Product;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'required' => true,
                'constraints' => [
                    new NotNull([
                        'message' => 'Le nom du produit ne peut pas être vide.'
                    ]),
                    new NotBlank(['message' => 'Le nom est requis.']),
                ]
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix (DT)',
                'required' => true,
                'scale' => 3,//chiffres apres le virgul 
                'html5' => true,
                'constraints' => [
                    new NotNull([
                        'message' => 'Le prix du produit ne peut pas être vide.'
                    ]),
                    new Positive([
                        'message' => 'Le prix doit être strictement supérieur à 0.'
                    ]),
                ]
                
                
            ])
            ->add('stock', NumberType::class, [
                'label' => 'Stock',
                'required' => true,
                'html5' => true,
                'constraints' => new Range([
            'min' => 1,
            'max' => 1000,
            'notInRangeMessage' => 'Le stock doit être entre {{ min }} et {{ max }}.'
        ]),
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name', // ou 'id' ou tout autre champ affiché dans le select
                'placeholder' => 'Choisir une catégorie',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'constraints' => new Length(min:10),
            ])
            ->add('images', TextType::class, [
                'label' => 'Images du produit',
                'mapped' => false,
                'required' => true,
                'constraints'=>[ new Url(['message'=>'Veuillez entrer une URL valide.']), 
               /* new Regex([
                    'pattern' => '/\.(jpg|jpeg|png)$/i',
                    'message' => 'L\'URL doit se terminer par .jpg, .jpeg, .png .',
                ]),*/],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-success']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
