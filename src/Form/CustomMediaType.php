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
                'label' => 'Nom de l\'objet / bolide',
                'attr' => [
                    'class' => 'appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm',
                    'placeholder' => 'ex: Ford Mustang 1967, Carburateur Weber, Flipper Addams Family...'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner un titre.'),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Catégorie de la collection',
                'choices' => [
                    '🚗 Voiture de Collection' => 'Voiture',
                    '🏍️ Moto de Collection' => 'Moto',
                    '⚙️ Pièce Détachée' => 'Pièce détachée',
                    '👾 Flipper Rétro' => 'Flipper',
                    '📻 Juke-box Rétro' => 'Juke-box',
                    '💿 Disque Vinyle' => 'Vinyle',
                    '💿 CD / Disque Compact' => 'CD',
                    '🎲 Jeu de Société' => 'Jeu de société',
                    '🎮 Jeu Vidéo' => 'Jeu vidéo',
                    '📼 K7 Vidéo / VHS' => 'K7 Vidéo',
                    '📻 Autre Collection / Thème personnalisé' => 'Autre',
                ],
                'attr' => [
                    'id' => 'custom_media_type_select',
                    'class' => 'appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700 mt-4'],
            ])
            ->add('customType', TextType::class, [
                'label' => 'Nom personnalisé de votre collection (Saisir si vous avez choisi "Autre" ci-dessus)',
                'required' => false,
                'mapped' => false, // Handled manually in the controller
                'attr' => [
                    'class' => 'appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm',
                    'placeholder' => 'ex: Dés à coudre, Vaches en porcelaine, Timbres...'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700 mt-4'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Détails, caractéristiques, lieu de stockage (ex: Caisse 3 Étagère C)',
                'required' => false,
                'mapped' => false, // Manually map to JSON attributes
                'attr' => [
                    'class' => 'appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm',
                    'placeholder' => "ex:\n- Stockage : Caisse 3 Étagère C\n- Prix d'achat : 150 €\n- Constructeur/Marque : Bally / Ford\n- État : Restauré",
                    'rows' => 4
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