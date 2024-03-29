<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,[
                'label' => 'Nom de la sortie'
            ])
            ->add('dateHeureDebut', DateTimeType::class,[
                'label' => 'Date et heure de la sortie',
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('dateLimiteInscription', DateType::class,[
                'label' => 'Date limite d\'inscription',
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('nbInscriptionsMax', IntegerType::class,[
                'label' => 'Nombre de places'
            ])
            ->add('duree', IntegerType::class,[
                'label' => 'Durée (min)'
            ])
            ->add('infosSortie', TextareaType::class,[
                'label' => 'Descriptions et infos'
            ])
            ->add('lieu',LieuType::class, [
                'label' => false
            ])
            ->add('campus',EntityType::class,[
                'class' => Campus::class,
                'label' => 'Campus',
                'choice_label' => 'nom',
            ])
            /*->add('organisateur',EntityType::class, [
                'class' => User::class,
                'label' => 'Organisateur'

            ])*/

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}