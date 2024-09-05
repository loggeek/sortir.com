<?php

namespace App\Form;

use App\Entity\campus;
use App\Entity\Excursion;
use App\Entity\Location;
use App\Entity\User;
use App\Enum\ExcursionStatus;
use Doctrine\DBAL\Types\StringType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExcursionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => false,
                ])
            ->add('date', DateTimeType::class, [
                'label' => 'Date',
                'view_timezone' => 'Europe/Paris',
                'required' => false,
            ])
            ->add('deadline', DateType::class, [
                'label' => 'Date limite d\'inscription',
                'required' => false,
            ])
            ->add('nb_seats', IntegerType::class, [
                'label' => 'Nombre de places',
                'required' => false,
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée (min)',
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'label' => 'Campus',
                'data' => $user ? $user->getCampus() : null,
            ])
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'choice_label' => 'name',
                'label' => 'Lieu',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Excursion::class,
        ]);

        $resolver->setRequired('user');
    }
}
