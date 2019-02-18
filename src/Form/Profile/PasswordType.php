<?php

namespace App\Form\Profile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PasswordType
 *
 * @package App\Form\Profile
 */
class PasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $option
     */
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('username', Type\TextType::class, [
                'label' => 'form.profile.username',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email()
                ]
            ])
            ->add('checkword', Type\TextType::class, [
                'label' => 'form.password.checkword',
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('password', Type\RepeatedType::class, [
                'type' => Type\PasswordType::class,
                'first_options' => ['label' => 'form.profile.password'],
                'second_options' => ['label' => 'form.profile.password_repeat'],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ]);
    }
}