<?php

namespace App\Form;

use App\Entity\UserItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CustomMediaCopyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('media', CustomMediaType::class, [
                'label' => false,
            ])
            ->add('condition', ChoiceType::class, [
                'label' => 'État physique de votre exemplaire / objet',
                'choices' => [
                    'Comme Neuf / Parfait état' => 'Mint',
                    'Très Bon État' => 'Very Good',
                    'Bon État' => 'Good',
                    'État Moyen / À restaurer' => 'Fair',
                    'Pour pièces / Non fonctionnel' => 'Poor',
                ],
                'attr' => [
                    'class' => 'appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700 mt-4'],
            ])
            ->add('personalNotes', TextareaType::class, [
                'label' => 'Notes privées de collectionneur (origine, historique, restaurations...)',
                'required' => false,
                'attr' => [
                    'class' => 'appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm',
                    'placeholder' => 'ex: Acheté à un passionné, restauration allumage effectuée en 2026...',
                    'rows' => 3
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700 mt-4'],
            ])
            ->add('image', FileType::class, [
                'label' => 'Photo de l\'objet (Optionnel - Max 5Mo, formats JPG/PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize: '5M',
                        maxSizeMessage: 'La photo ne doit pas dépasser 5 Mo.',
                        mimeTypes: [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        mimeTypesMessage: 'Veuillez téléverser un format valide (JPG, PNG, WEBP).'
                    )
                ],
                'attr' => [
                    'class' => 'appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700 mt-4'],
            ])
            ->add('valuation', IntegerType::class, [
                'label' => 'Valeur estimée / Côte d\'expertise (en €, Optionnel)',
                'required' => false,
                'mapped' => false, // Handled manually in the controller JSON
                'attr' => [
                    'class' => 'appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm',
                    'placeholder' => 'ex: 25000'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700 mt-4'],
            ])
            ->add('valuationDate', DateType::class, [
                'label' => 'Date de l\'expertise / estimation (Optionnel)',
                'required' => false,
                'mapped' => false, // Handled manually in the controller JSON
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700 mt-4'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserItem::class,
        ]);
    }
}