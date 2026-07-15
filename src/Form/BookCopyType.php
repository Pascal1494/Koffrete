<?php

namespace App\Form;

use App\Entity\UserItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookCopyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('media', BookType::class, [
                'label' => false,
            ])
            ->add('condition', ChoiceType::class, [
                'label' => 'État physique de votre exemplaire',
                'choices' => [
                    'Comme Neuf / Mint' => 'Mint',
                    'Très Bon État / Very Good' => 'Very Good',
                    'Bon État / Good' => 'Good',
                    'État Moyen / Fair' => 'Fair',
                    'Abîmé / Poor' => 'Poor',
                ],
                'attr' => [
                    'class' => 'appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700 mt-4'],
            ])
            ->add('personalNotes', TextareaType::class, [
                'label' => 'Notes personnelles (édition, origine, dédicace...)',
                'required' => false,
                'attr' => [
                    'class' => 'appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm',
                    'placeholder' => 'ex: Édition spéciale reliée, achetée en brocante...',
                    'rows' => 3
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