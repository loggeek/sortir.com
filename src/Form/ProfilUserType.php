<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfilUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Pseudo : '
            ])
            ->add('name', TextType::class, [
                'label' => 'Prénom : '
            ])
            ->add('surname', TextType::class, [
                'label' => 'Nom : '
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone : '
            ])
            ->add('email',  EmailType::class, [
                'label' => 'Email : '
            ])
            ->add('password', RepeatedType::class, [

                'type' => PasswordType::class,

                'required' => false,

                'first_options'  => [
                    'label' => 'Mot de passe : ',
                ],
                'second_options' => [
                    'label' => 'Confirmation : ', // Ce champ de confirmation n'est pas directement mappé à l'entité "User"e,
                ],
                'invalid_message' => 'Les deux mots de passe doivent être identiques',

                'mapped' => false,

//                'constraints' => [
//                    new NotBlank([
//                        'message' => 'Veuillez renseigner le nouveau mot de passe',
//                    ]),
//                    new Length([
//                        'min' => 5,
//                        'minMessage' => ' Votre mot de passe doit contenir au moins {{ limit }} caractères',
//                        'max' => 4096,
//                    ]),
//                ],
            ])
            ->add('campus', EntityType::class, [
                    'class' => Campus::class,
                    'choice_label' => 'name',
                    'label' => 'Campus',
                    'required' => false,
            ])
            ->add('filePlaceholder', TextType::class, [
                'mapped' => false, // Ce champ n'est pas mappé à l'entité
                'label' => 'Ma photo : ',
                'help' => 'Le format de l\'image doit être en PNG ou en JPEG...',
                'attr' => [
                    'placeholder' => 'Télécharger le fichier vers le serveur',
                    'readonly' => true,
                ],
            ])
            ->add('profileImage', FileType::class, [
                'label' => false,
                'required' => false,
                'data_class' => null,
                'attr' => [
                    'style' => 'display:none;', // Masquer le champ file
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
