<?php

namespace App\Form;

use App\Entity\CustomMedia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomMediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'œuvre',
                'attr' => [
                    'class' => 'appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm',
                    'placeholder' => 'ex: Abbey Road, PS5 FIFA 24, Monopoly...'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner le titre.'),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de média',
                'choices' => [
                    'Disque Vinyle' => 'Vinyle',
                    'CD / Disque Compact' => 'CD',
                    'Jeu de Société' => 'Jeu de société',
                    'Jeu Vidéo' => 'Jeu vidéo',
                    'K7 Audio / Cassette' => 'K7 Audio',
                    'K7 Vidéo / VHS' => 'K7 Vidéo',
                    'Blu-ray' => 'Blu-ray',
                    'Collection Personnalisée / Autre' => 'Autre',
                ],
                'attr' => [
                    'class' => 'appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700 mt-4'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Caractéristiques spécifiques (Morceaux, Plateforme, Nb joueurs...)',
                'required' => false,
                'mapped' => false, // We will manually map this to the attributes JSON array
                'attr' => [
                    'class' => 'appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm',
                    'placeholder' => 'ex: Console: PS5, Joueurs: 2-6, Tracklist: ...',
                    'rows' => 3
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700 mt-4'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomMedia::class,
        ]);
    }
}