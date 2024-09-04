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
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExcursionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom',])
            ->add('date', null, [
                'widget' => 'single_text',
                'label' => 'Date',
            ])
            ->add('deadline', null, [
                'widget' => 'single_text',
                'label' => 'Date limite d\'inscription',
            ])
            ->add('nb_seats', IntegerType::class, ['label' => 'Nombre de places',])
            ->add('duration', IntegerType::class, ['label' => 'Durée (min)',])
            ->add('description', TextType::class, ['label' => 'Description',])
//            ->add('status', EnumType::class, [
//                'class' => ExcursionStatus::class,
//                'label' => 'Statut',])
//            ->add('organizer', EntityType::class, [
//                'class' => User::class,
//                'choice_label' => 'username',
//                'label' => 'Organisateur',
//            ])
//            ->add('participants', EntityType::class, [
//                'class' => User::class,
//                'choice_label' => 'username',
//                'multiple' => true,
//                'label' => 'Participants',
//            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'label' => 'Campus',
            ])
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'choice_label' => 'name',
                'label' => 'Lieu',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Excursion::class,
        ]);
    }
}
