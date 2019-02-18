<?php

namespace App\Form\Profile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ResetType
 *
 * @package App\Form\Profile
 */
class ResetType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $option
     */
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'form.reset.username',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email()
                ]
            ]);
    }
}