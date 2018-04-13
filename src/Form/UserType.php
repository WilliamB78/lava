<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'required' => true,
                'label' => 'Votre prénom',
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Saisissez le Prénom',
                ],
            ])
            ->add('lastname', TextType::class, [
                'required' => true,
                'label' => 'Votre nom',
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Saisissez le Nom',
                ],
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Saisissez l\'email',
                ],
            ])
            ->add('password', PasswordType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'input',
                    'placeholder' => '***********',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
