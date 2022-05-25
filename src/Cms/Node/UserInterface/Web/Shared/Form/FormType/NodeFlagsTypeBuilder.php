<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Shared\Form\FormType;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeBuilder\AbstractFieldTypeBuilder;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;
use Tulia\Cms\Node\Domain\NodeFlag\NodeFlagRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeFlagsTypeBuilder extends AbstractFieldTypeBuilder
{
    private NodeFlagRegistryInterface $flagRegistry;
    private TranslatorInterface $translator;

    public function __construct(NodeFlagRegistryInterface $flagRegistry, TranslatorInterface $translator)
    {
        $this->flagRegistry = $flagRegistry;
        $this->translator = $translator;
    }

    public function buildOptions(Field $field, array $options, ContentType $contentType): array
    {
        $availableFlags = [];

        foreach ($this->flagRegistry->all() as $type => $flag) {
            $availableFlags[$this->translator->trans($flag['label'], [], 'node')] = $type;
        }

        $options['translation_domain'] = 'node';
        $options['choices'] = $availableFlags;
        $options['help'] = 'nodePurposeInfo';
        $options['choice_translation_domain'] = false;
        $options['multiple'] = true;

        return $options;
    }
}
