<?php

namespace App\Form;

use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AccessType
 * @package App\Form
 */
class AccessType extends AbstractType
{
    /**
     * Scope constants
     */
    const SCOPE_NODE = 0;
    const SCOPE_CHILDREN = 1;

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
            ->add('items', Type\HiddenType::class)
            ->add('roles', EntityType::class, [
                'required' => false,
                'label' => 'form.roles.label',
                'attr' => ['size' => 6],
                'class' => Role::class,
                'choice_label' => 'title',
                'choice_translation_domain' => false,
                'choice_attr' => function (Role $choice) {
                    return ['help' => $choice->getDescription()];
                },
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('scope', Type\ChoiceType::class, [
                'label' => 'form.scope.label',
                'expanded' => true,
                'choices' => $this->getScopes(),
                'data' => self::SCOPE_NODE
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
            'translation_domain' => 'access',
        ]);
    }

    /**
     * @return array
     */
    protected function getScopes(): array
    {
        $applications = [
            self::SCOPE_NODE => 'form.scope.node',
            self::SCOPE_CHILDREN => 'form.scope.children',
        ];

        return array_flip(array_map([$this, 'translate'], $applications));
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function translate(string $value): string
    {
        return $this->translator->trans($value, [], 'access');
    }
}
