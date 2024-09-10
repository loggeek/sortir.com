<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Town;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Lieu : '
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse : '
            ])
            ->add('latitude', TextType::class, [
                'label' => 'Latitude',
            ])

            ->add('longitude', TextType::class, [
                'label' => 'Longitude',
            ])
            ->add('town', EntityType::class, [
                'class' => Town::class,
                'label' => 'Ville',
                'choice_label' => 'id',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
