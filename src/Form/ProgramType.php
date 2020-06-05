<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Program;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Actor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProgramType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Nom'])
            ->add('summary', TextType::class, ['label' => 'Résumé'])
            ->add('poster', UrlType::class)
            ->add('category', EntityType::class, [
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'class' => Category::class
            ])
            ->add('actors', EntityType::class, [
                'choice_label' => 'name',
                'label' => 'Acteur·rice·s',
                'class' => Actor::class,
                'multiple' => true,
                'by_reference' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Program::class,
        ]);
    }
}
