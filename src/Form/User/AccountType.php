<?php

namespace App\Form\User;

use App\Entity\User\Account;
use App\Repository\Account\AccountRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AccountType
 *
 * @package App\Form\User
 */
class AccountType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('account', EntityType::class, [
                'label' => 'form.account.label',
                'help' => 'form.account.help',
                'placeholder' => 'form.account.placeholder',
                'class' => \App\Entity\Account\Account::class,
                'choice_label' => 'title',
                'query_builder' => function (AccountRepository $repository) {
                    return $repository->available();
                },
            ])
            ->add('position', TextType::class, [
                'required' => false,
                'label' => 'form.position.label',
                'help' => 'form.position.help',
                'attr' => ['placeholder' => 'form.position.placeholder']
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Account::class
        ]);
    }
}
