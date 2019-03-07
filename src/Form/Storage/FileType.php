<?php

namespace App\Form\Storage;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class FileType
 * @package App\Form\Storage
 */
class FileType extends AbstractType
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
        $builder
            ->add('name', Type\TextType::class, [
                'label' => 'form.file.title.label',
                'help' => 'form.file.title.help',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 250])
                ]
            ])
            ->add('options', Type\ChoiceType::class, [
                'label' => false,
                'choices' => array_flip([
                    'translit' => $this->translator->trans('form.file.options.use_translit', [], 'storage'),
                    'underscore' => $this->translator->trans('form.file.options.replace_with_underscore', [], 'storage')
                ]),
                'multiple' => true,
                'expanded' => true,
                'data' => ['translit', 'underscore']
            ])
            // This hidden field is required for Workflow update
            ->add('updatedAt', Type\HiddenType::class, [ 'data' => time() ])
            ->add('cancel', Type\ButtonType::class, [
                'label' => 'form.button.cancel',
                'translation_domain' => 'messages',
                'attr' => ['class' => 'btn btn--default', 'data-dismiss' => 'modal']
            ])
            ->add('save', Type\SubmitType::class, [
                'label' => 'form.button.save',
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
            'translation_domain' => 'storage',
        ]);
    }
}
