<?php

namespace App\Form;

use App\Entity\Brands;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BrandsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a Brand Name']),
                    
                ],
                'required'  => false,
    
                ])
            ->add('image', FileType::class, [
                'label' => 'Image (JPEG,JPG or PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Please Upload a Brand Logo']),

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
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Active' => true,
                    'Inactive' => false,
                ],
                'data' => true,
                
                'required'  => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Brands::class,
        ]);
    }
}
