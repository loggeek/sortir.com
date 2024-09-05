<?php

namespace App\Form;

use App\DTO\ExcursionFilter;
use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExcursionFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'label' => 'Campus',
                'required' => false,
                'placeholder' => 'Tous les campus',
                'data' => $user ? $user->getCampus() : null // TODO understand what the f... that means
            ])
            ->add('name', TextType::class, [
                'label' => 'Rechercher la sortie',
                'required' => false,
            ])
            ->add('datemin', DateType::class, [ // TODO améliorer la présentation des champs des dates
                'label' => 'Date minimum',
                'required' => false,
            ])
            ->add('datemax', DateType::class, [
                'label' => 'Date maximum',
                'required' => false,
            ])
            ->add('organizer', CheckboxType::class, [
                'label' => 'Uniquement les sorties que j\'organise',
                'required' => false,
            ])
            ->add('participating', CheckboxType::class, [
                'label' => ' Uniquement les sorties auxquelles je participe',
                'required' => false,
            ])
            ->add('nonparticipating', CheckboxType::class, [
                'label' => 'Exclure les sorties auxquelles je participe',
                'required' => false,
            ])
            ->add('showpast', CheckboxType::class, [
                'label' => 'Afficher les sorties passées',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExcursionFilter::class,
        ]);

        $resolver->setRequired('user');
    }
}
