<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Topic;
use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message', TextareaType::class, ['label' => 'Post'])
            ->add('utilisateur', EntityType::class, ['class' => Utilisateur::class, 'choice_label' => 'pseudo'])
            ->add('topic', EntityType::class, ['class' => Topic::class, 'choice_label' => 'titre'])
            ->add('submit',SubmitType::class, ['attr' => ['class' => 'btn']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
