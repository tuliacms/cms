<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\ContentType\Infrastructure\Presentation;

use Symfony\Component\Form\FormView;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\ContentType\Domain\WriteModel\Routing\Strategy\ContentTypeRoutingStrategyRegistry;
use Tulia\Cms\ContentBuilder\ContentType\Domain\WriteModel\Service\Configuration;
use Tulia\Cms\ContentBuilder\Layout\Service\FieldTypeMappingRegistry;
use Tulia\Cms\ContentBuilder\Layout\Service\LayoutTypeBuilderInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class TwigRoutableContentTypeLayoutBuilder implements LayoutTypeBuilderInterface
{
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;
    private ContentTypeRoutingStrategyRegistry $strategyRegistry;
    private TranslatorInterface $translator;
    private Configuration $configuration;

    public function __construct(
        FieldTypeMappingRegistry $fieldTypeMappingRegistry,
        ContentTypeRoutingStrategyRegistry $strategyRegistry,
        TranslatorInterface $translator,
        Configuration $configuration
    ) {
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
        $this->strategyRegistry = $strategyRegistry;
        $this->translator = $translator;
        $this->configuration = $configuration;
    }

    public function editorView(ContentType $contentType, FormView $formView, array $viewContext): View
    {
        return new View('@backend/content_builder/layout/routable_content_type/editor.tpl', [
            'contentType' => $contentType,
            'layout' => $contentType->getLayout(),
            'form' => $formView,
            'context' => $viewContext,
        ]);
    }

    public function builderView(string $contentType, array $data, array $errors, bool $creationMode): View
    {
        return new View('@backend/content_builder/layout/routable_content_type/builder.tpl', [
            'fieldTypes' => $this->getFieldTypes($contentType),
            'routingStrategies' => $this->getRoutingStrategies($contentType),
            'model' => $data,
            'errors' => $errors,
            'multilingual' => $this->configuration->isMultilingual($contentType),
            'creationMode' => $creationMode,
        ]);
    }

    private function getFieldTypes(string $contentType): array
    {
        $types = [];

        foreach ($this->fieldTypeMappingRegistry->allForContentType($contentType) as $type => $data) {
            $types[$type] = [
                'id' => $type,
                'label' => $data['label'],
                'configuration' => $data['configuration'],
                'constraints' => $data['constraints'],
            ];
        }

        return $types;
    }

    private function getRoutingStrategies(string $contentType): array
    {
        $strategies = [];

        foreach ($this->strategyRegistry->all() as $strategy) {
            if ($strategy->supports($contentType) === false) {
                continue;
            }

            $strategies[] = [
                'id' => $strategy->getId(),
                'label' => $this->translator->trans(sprintf('contentTypeStrategy_%s', $strategy->getId()), [], 'content_builder'),
            ];
        }

        return $strategies;
    }
}
