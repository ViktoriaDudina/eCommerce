<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'row_attr' => [ 'class' => 'col-md-12 mb-3'],
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a Title']),
                    
                ],
                'required'  => false,
    
                ])
            ->add('image', FileType::class, [
                'row_attr' => [ 'class' => 'col-md-12 mb-3'],
                'attr' => [ 'class'=> 'form-control'],
                'label' => 'Image (JPEG,JPG or PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPEG,JPG or PNG image',
                    ])
                ],  
            ])
            ->add('summary', null, [
                'row_attr' => [ 'class' => 'col-md-12 mb-3'],
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Please write a Description']),
                    
                ],
                'required'  => false,
    
                ])
            ->add('description', null, [
                'row_attr' => [ 'class' => 'col-md-12 mb-3'],
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Please write a Description']),
                    
                ],
                'required'  => false,
    
                ])
            ->add('price', null, [
                'row_attr' => [ 'class' => 'col-md-5 mb-3'],
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a Price']),
                    
                ],
                'required'  => false,
    
                ])
            ->add('quantity', null, [
                'row_attr' => [ 'class' => 'col-md-5 mb-3'],
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a Price']),
                    
                ],
                'required'  => false,
    
                ])
            ->add('on_sale', ChoiceType::class, [
                'choices' => [
                    'No' => false,
                    'Yes' => true,
                ],
                'row_attr' => [ 'class' => 'col-md-4 '],
                'attr' => ['class' => 'form-select'],
                
                'required'  => false,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Active' => true,
                    'Inactive' => false,
                ],
                'data' => true,
                'row_attr' => [ 'class' => 'col-md-4 '],
                'attr' => ['class' => 'form-select'],
                
                'required'  => false,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'row_attr' => [ 'class' => 'col-md-4'],
                'attr' => ['class' => 'form-select'],
                'constraints' => [
                    new NotBlank(['message' => 'Please Select a Category']),
                    
                ],
                
                'required'  => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
