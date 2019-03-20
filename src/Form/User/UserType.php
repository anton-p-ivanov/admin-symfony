<?php

namespace App\Form\User;

use App\Entity\Role;
use App\Entity\Site;
use App\Entity\User\Password;
use App\Entity\User\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserType
 * @package App\Form\User
 */
class UserType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * FileType constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* @var User $user */
        $user = $builder->getData();

        $builder
            ->add('isActive', Type\ChoiceType::class, [
                'label' => 'form.isActive.label',
                'expanded' => true,
                'choices' => $this->getBooleanChoices(),
                'attr' => ['class' => 'form__choices--inline'],
                'constraints' => [
                    new Assert\Choice(['choices' => $this->getBooleanChoices()])
                ]
            ])
            ->add('isConfirmed', Type\ChoiceType::class, [
                'label' => 'form.isConfirmed.label',
                'expanded' => true,
                'choices' => $this->getBooleanChoices(),
                'attr' => ['class' => 'form__choices--inline'],
                'constraints' => [
                    new Assert\Choice(['choices' => $this->getBooleanChoices()])
                ]
            ])
            ->add('email', Type\TextType::class, [
                'label' => 'form.email.label',
                'help' => 'form.email.help',
                'attr' => ['placeholder' => 'form.email.placeholder'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ]
            ])
            ->add('fname', Type\TextType::class, [
                'label' => 'form.fname.label',
                'help' => 'form.fname.help',
                'attr' => ['placeholder' => 'form.fname.placeholder'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('lname', Type\TextType::class, [
                'label' => 'form.lname.label',
                'help' => 'form.lname.help',
                'attr' => ['placeholder' => 'form.lname.placeholder'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('sname', Type\TextType::class, [
                'required' => false,
                'label' => 'form.sname.label',
                'help' => 'form.sname.help',
                'attr' => ['placeholder' => 'form.sname.placeholder'],
                'constraints' => [
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('phone', Type\TextType::class, [
                'required' => false,
                'label' => 'form.phone.label',
                'help' => 'form.phone.help',
                'attr' => ['placeholder' => 'form.phone.placeholder'],
                'constraints' => [
                    new Assert\Length(['max' => 50]),
                    new Assert\Regex(['pattern' => '/^(\+(\d{1,3})\-(\d+)\-(\d+)){1,18}\s*(#\d+)?$/'])
                ]
            ])
            ->add('phoneMobile', Type\TextType::class, [
                'required' => false,
                'label' => 'form.phoneMobile.label',
                'help' => 'form.phoneMobile.help',
                'attr' => ['placeholder' => 'form.phoneMobile.placeholder'],
                'constraints' => [
                    new Assert\Length(['max' => 50]),
                    new Assert\Regex(['pattern' => '/^(\+(\d{1,3})\-(\d+)\-(\d+)){1,18}\s*(#\d+)?$/'])
                ]
            ])
            ->add('skype', Type\TextType::class, [
                'required' => false,
                'label' => 'form.skype.label',
                'help' => 'form.skype.help',
                'attr' => ['placeholder' => 'form.skype.placeholder'],
                'constraints' => [
                    new Assert\Length(['max' => 200])
                ]
            ])
            ->add('birthdate', Type\BirthdayType::class, [
                'required' => false,
                'label' => 'form.birthdate.label',
                'help' => 'form.birthdate.help',
                'widget' => 'single_text',
                'placeholder' => 'form.birthdate.placeholder',
                'attr' => ['pattern' => '\d\d\d\d-\d\d-\d\d'],
                'constraints' => [
                    new Assert\Date(),
                    new Assert\LessThan(['value' => new \DateTime()])
                ]
            ])
            ->add('roles', EntityType::class, [
                'required' => false,
                'label' => false,
                'class' => Role::class,
                'choice_label' => 'title',
                'choice_translation_domain' => false,
                'choice_attr' => function(Role $choice) {
                    return ['help' => $choice->getDescription()];
                },
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('sites', EntityType::class, [
                'required' => false,
                'label' => false,
                'class' => Site::class,
                'choice_label' => 'title',
                'choice_translation_domain' => false,
                'choice_attr' => function (Site $choice) {
                    return ['help' => $choice->getDescription()];
                },
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('account', AccountType::class)
            ->add('password', Type\RepeatedType::class, [
                'required' => false,
                'type' => Type\PasswordType::class,
                'first_options' => [
                    'label' => 'form.password.label',
                    'attr' => ['placeholder' => 'form.password.placeholder']
                ],
                'second_options' => [
                    'label' => 'form.password_repeat.label',
                    'attr' => ['placeholder' => 'form.password_repeat.placeholder']
                ]
            ])
            // This hidden field is required for Workflow update
            ->add('updatedAt', Type\HiddenType::class, [ 'data' => time() ])
            ->add('save', Type\SubmitType::class, [
                'label' => 'form.button.save',
                'translation_domain' => 'messages',
                'attr' => ['class' => 'btn btn--primary']
            ])
            ->add('apply', Type\SubmitType::class, [
                'label' => 'form.button.apply',
                'translation_domain' => 'messages',
                'attr' => ['class' => 'btn btn--default']
            ]);

        $builder->get('password')->addModelTransformer(new CallbackTransformer(
            function ($password) { return $password; },
            function ($password) use ($user) {
                if ($password) {
                    return new Password($password, $user);
                }

                return $password;
            }
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'users'
        ]);
    }

    /**
     * @return array
     */
    protected function getBooleanChoices(): array
    {
        return array_flip([
            0 => $this->translate('general.booleans.no'),
            1 => $this->translate('general.booleans.yes')
        ]);
    }

    /**
     * @param string $message
     * @param array $params
     * @param string|null $domain
     *
     * @return string
     */
    private function translate(string $message, array $params = [], string $domain = null): string
    {
        return $this->translator->trans($message, $params, $domain);
    }
}
