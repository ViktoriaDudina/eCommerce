<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'ROLE_USER' => 'ROLE_USER',
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                    // Add other roles as needed
                ],
                'multiple' => true,
                'expanded' => false,
                'choice_label' => null,
            ])
            ->add('password', PasswordType::class)
            ->add('Name')
            ->add('phone_number')
            ->add('avatar')
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Active' => true,
                    'Block' => false,
                ],
                'data' => true,
                'row_attr' => [ 'class' => 'col-md-4 '],
                'attr' => ['class' => 'form-select'],
                
                'required'  => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
