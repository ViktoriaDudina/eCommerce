<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Reviews;
use App\Entity\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comments', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a Review']),
                    
                ],
                'required'  => false,
                'label' => 'Review',
    
                ])
            // ->add('ratings')
            
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reviews::class,
        ]);
    }
}
