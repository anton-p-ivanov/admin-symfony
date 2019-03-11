<?php

namespace App\Form;

use App\Validator\Constraints\Password;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ConfirmType
 * @package App\Form
 */
class ConfirmType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', Type\PasswordType::class, [
                'label' => 'form.confirm.password.label',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Password()
                ]
            ])
            ->add('cancel', Type\ButtonType::class, [
                'label' => 'form.button.cancel',
                'translation_domain' => 'messages',
                'attr' => ['class' => 'btn btn--default', 'data-dismiss' => 'modal']
            ])
            ->add('confirm', Type\SubmitType::class, [
                'label' => 'form.button.confirm',
                'translation_domain' => 'messages',
                'attr' => ['class' => 'btn btn--primary']
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'spreadsheet',
        ]);
    }
}
