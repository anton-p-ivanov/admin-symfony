<?php

namespace App\Form\Storage;

use App\Entity\Role;
use App\Entity\Storage\Category;
use App\Entity\Storage\Storage;
use App\Entity\Storage\Tree;
use App\Entity\WorkflowStatus;
use App\Repository\Storage\CategoryRepository;
use App\Repository\Storage\TreeRepository;
use App\Repository\Workflow\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class StorageType
 * @package App\Form\Storage
 */
class StorageType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * StorageType constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* @var Storage $storage */
        $storage = $builder->getData();

        $builder
            ->add('title', Type\TextType::class, [
                'label' => 'form.storage.title.label',
                'help' => 'form.storage.title.help',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 250])
                ]
            ])
            ->add('description', Type\TextareaType::class, [
                'required' => false,
                'label' => 'form.storage.description.label',
                'help' => 'form.storage.description.help',
                'attr' => ['rows' => 5],
                'constraints' => [
                    new Assert\Length(['max' => 1000])
                ]
            ])
            ->add('content', Type\TextareaType::class, [
                'required' => false,
                'label' => 'form.storage.content.label',
                'help' => 'form.storage.content.help',
                'attr' => ['rows' => 10],
                'constraints' => [
                    new Assert\Length(['max' => 5000])
                ]
            ])
            ->add('categories', EntityType::class, [
                'required' => false,
                'label' => 'form.storage.categories.label',
                'help' => 'form.storage.categories.help',
                'attr' => ['size' => 6],
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
                'query_builder' => function (CategoryRepository $repository) {
                    return $repository->available();
                },
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('parent', EntityType::class, [
                'label' => 'form.storage.node.label',
                'help' => 'form.storage.node.help',
                'class' => Tree::class,
                'query_builder' => function (TreeRepository $repository) use ($storage) {
                    return $repository->getTree($storage->getNode());
                },
                'choice_label' => function (Tree $node) {
                    return $this->getNodeTitle($node);
                },
                'constraints' => [
                    new Assert\NotBlank()
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
            ->add('status', EntityType::class, [
                'label' => 'form.storage.status.label',
                'class' => WorkflowStatus::class,
                'choice_label' => 'title',
                'expanded' => true,
                'choice_translation_domain' => false,
                'query_builder' => function (StatusRepository $repository) {
                    return $repository->available();
                },
                'constraints' => [
                    new Assert\NotBlank()
                ],
                'data' => $builder->getData()->getStatus() ?? $this->getDefaultStatus()
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
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'storage'
        ]);
    }

    /**
     * @param Tree $node
     *
     * @return string
     */
    protected function getNodeTitle(Tree $node): string
    {
        return str_repeat('-- ', $node->getLevel())
            . ($node->getStorage() ? $node->getStorage()->getTitle() : 'File storage');
    }

    /**
     * @return WorkflowStatus
     */
    protected function getDefaultStatus(): WorkflowStatus
    {
        return $this->manager->getRepository(WorkflowStatus::class)->findOneBy(['isDefault' => true]);
    }
}
