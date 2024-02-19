<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Wish;
use App\Repository\CategoryRepository;
use App\Repository\WishRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Wish title',

                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                    "class" => "couleurfonddiv"
                ]
            ])
            ->add('description', TextareaType::class,[
                'required' => false,
                'attr' => [
                    "class" => "couleurfonddiv",
                ]])
            ->add('author', TextType::class,[
                'required' => false,
                'attr' => [
                    "class" => "couleurfonddiv",
                ]])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'required' => false,
                'choice_label' => 'name',
                'placeholder' => '--- Choose the category ---',
                'attr' => [
                    "class" => "couleurfonddiv",
                ]
            ])
            ->add('isPublished', CheckboxType::class, [
                'data' => true
            ])

            ->add('poster_file', FileType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => [
                    "class" => "couleurfonddiv",
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => "Ce format est pas ok",
                        'maxSizeMessage' => "Ce fichier est trop lourd",
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Wish::class,
        ]);
    }
}
