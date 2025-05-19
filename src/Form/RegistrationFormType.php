<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('lastname', TextType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Le nom est requis.']),
            ],
        ])
        ->add('firstname', TextType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Le prénom est requis.']),
            ],
        ])
        ->add('adress')
        ->add('telephone', TelType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Le numéro de téléphone est requis.']),
                new Regex([
                    'pattern' => '/^\d{8}$/',
                    'message' => 'Le numéro de téléphone doit contenir exactement 8 chiffres.',
                ]),
            ],
        ])
        ->add('imageProfile', TextType::class, [
            'required' => false,
            'constraints'=>[ new Url(['message'=>'Veuillez entrer une URL valide.'])]
            // Ajouter des contraintes si besoin
        ])
        ->add('email', EmailType::class, [
            'constraints' => [
                new Email(['message' => 'L\'email n\'est pas valide.']),
            ],
        ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
