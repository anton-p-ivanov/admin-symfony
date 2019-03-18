<?php

namespace App\Form\Profile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CheckwordType
 *
 * @package App\Form\Profile
 */
class CheckwordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $option
     */
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('username', Type\TextType::class, [
                'label' => 'form.checkword.username',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email()
                ]
            ])
            ->add('resend', Type\SubmitType::class, [
                'label' => 'form.checkword.resend',
                'translation_domain' => 'messages',
                'attr' => ['class' => 'btn btn--primary btn--block']
            ]);
    }
}