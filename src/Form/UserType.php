<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('pseudo')
            ->add('lastname')
            ->add('firstname')
            ->add('phoneNumber')
            ->add('userCampus', EntityType::class, [
                    'class' => Campus::class,
                    'choice_label' => 'name',
                    // 'disabled' => true
                ]
            )
            ->add('avatar', FileType::class, [
                'mapped' => false,
                'label' => 'if you want to have some avatar :',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}