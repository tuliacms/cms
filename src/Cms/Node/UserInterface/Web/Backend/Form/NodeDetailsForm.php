<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Backend\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormType\AttributesAwareFormTypeTrait;
use Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormType\AttributesType;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodePurpose\NodePurposeRegistryInterface;
use Tulia\Cms\Node\UserInterface\Web\Backend\Form\FormType\NodeCategoryTypeaheadType;
use Tulia\Cms\Options\Domain\ReadModel\Options;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\DateTimeType;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Cms\User\Infrastructure\Framework\Form\FormType\UserTypeahead\UserTypeaheadType;

/**
 * @author Adam Banaszkiewicz
 */
final class NodeDetailsForm extends AbstractType
{
    use AttributesAwareFormTypeTrait {
        AttributesAwareFormTypeTrait::configureOptions as traitConfigureOptions;
    }

    public function __construct(
        private readonly NodePurposeRegistryInterface $flagRegistry,
        private readonly TranslatorInterface $translator,
        private readonly AuthenticatedUserProviderInterface $authenticatedUserProvider,
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
        private readonly Options $options,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('id', HiddenType::class);
        $builder->add('title', TextType::class, [
            'label' => 'title',
            'constraints' => [
                new Assert\NotBlank()
            ]
        ]);
        $builder->add('published_at', DateTimeType::class, [
            'label' => 'publishedAt',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\DateTime()
            ]
        ]);
        $builder->add('published_to', DateTimeType::class, [
            'label' => 'publicationEndsAt',
            'constraints' => [
                new Assert\DateTime()
            ]
        ]);
        $builder->add('status', ChoiceType::class, [
            'label' => 'publicationStatus',
            'choices' => [
                'Draft' => 'draft',
                'Published' => 'published',
                'Trashed' => 'trashed',
            ],
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Choice([ 'choices' => ['draft', 'published', 'trashed'] ]),
            ],
        ]);
        $builder->add('author_id', UserTypeaheadType::class, [
            'label' => 'author',
            'constraints' => [
                new Assert\NotBlank()
            ],
            'empty_data' => $this->authenticatedUserProvider->getUser()->getId(),
            'website_id' => $options['website']->getId(),
            'locale' => $options['website']->getLocale()->getCode(),
        ]);
        $builder->add('purposes', ChoiceType::class, [
            'label' => 'nodePurpose',
            'help' => 'nodePurposeInfo',
            'translation_domain' => 'node',
            'choices' => $this->buildPurposes(),
            'choice_translation_domain' => false,
            'multiple' => true,
        ]);
        $builder->add('attributes', AttributesType::class, [
            'partial_view' => $options['partial_view'],
            'website' => $options['website'],
            'content_type' => $options['content_type'],
        ]);

        /*if ($contentType->isHierarchical()) {
            $builder->add('parent_id', NodeTypeaheadType::class, [
                'translation_domain' => 'node',
                'label' => 'parentNode',
                'search_route_params' => [
                    'node_type' => $contentType->getCode(),
                ],
                'constraints' => [
                    new Assert\Callback(function ($value, ExecutionContextInterface $context) {
                        if (empty($value) === false && $value === $context->getRoot()->get('id')->getData()) {
                            $context->buildViolation('cannotAssignSelfNodeParent')
                                ->setTranslationDomain('node')
                                ->atPath('parent_id')
                                ->addViolation();
                        }
                    }),
                ],
            ]);
        }*/

        if ($this->contentTypeRegistry->get($options['content_type'])->isRoutable()) {
            $builder->add('slug', TextType::class, [
                'constraints' => [
                    // new UniqueSlug()
                ]
            ]);
        }

        $categoryTaxonomy = $this->options->get(sprintf('node.%s.category_taxonomy', $options['content_type']));

        if ($categoryTaxonomy) {
            $builder->add('main_category', NodeCategoryTypeaheadType::class, [
                'taxonomy_type' => $categoryTaxonomy,
                'label' => 'mainCategory',
                'help' => 'mainCategoryHelp',
                'translation_domain' => 'node',
                'website_id' => $options['website']->getId(),
                'locale' => $options['website']->getLocale()->getCode(),
            ]);
            $builder->add('additional_categories', NodeCategoryTypeaheadType::class, [
                'taxonomy_type' => $categoryTaxonomy,
                'multiple' => true,
                'label' => 'additionalCategories',
                'help' => 'additionalCategoriesHelp',
                'translation_domain' => 'node',
                'website_id' => $options['website']->getId(),
                'locale' => $options['website']->getLocale()->getCode(),
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $this->traitConfigureOptions($resolver);

        $resolver->setDefault('attr', ['class' => 'tulia-dynamic-form']);
    }

    private function buildPurposes(): array
    {
        $purposes = [];

        foreach ($this->flagRegistry->all() as $type => $purpose) {
            $purposes[$this->translator->trans($purpose['label'], [], 'node')] = $type;
        }

        return $purposes;
    }
}
