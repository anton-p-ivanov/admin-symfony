<?php

namespace App\Form\Profile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ConfirmType
 *
 * @package App\Form\Profile
 */
class ConfirmType extends AbstractType
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
                'label' => 'form.confirm.checkword',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 10, 'max' => 10])
                ]
            ])
            ->add('password', Type\PasswordType::class, [
                'label' => 'form.profile.password',
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ]);
    }
}