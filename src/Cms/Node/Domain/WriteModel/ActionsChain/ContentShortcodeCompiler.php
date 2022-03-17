<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\ActionsChain;

use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AggregateRoot;
use Tulia\Component\Shortcode\ProcessorInterface;

/**
 * Listener is responsible for parsing and compiling Node's source
 * content, and saving this content into `content` field on Node.
 * All operations are done while create or update node at backend.
 *
 * @author Adam Banaszkiewicz
 */
class ContentShortcodeCompiler implements AggregateActionInterface
{
    protected ProcessorInterface $processor;

    public function __construct(ProcessorInterface $processor)
    {
        $this->processor = $processor;
    }

    public static function listen(): array
    {
        return [
            'create' => 100,
            'update' => 100,
        ];
    }

    public static function supports(): string
    {
        return Node::class;
    }

    public function execute(AggregateRoot $node): void
    {
        /** @var Node $node */
        foreach ($node->getAttributes() as $attribute) {
            if (! $attribute->getValue()) {
                continue;
            }

            if ($attribute->isCompilable() === false) {
                continue;
            }

            $data = $attribute->toArray();
            $data['compiled_value'] = $this->processor->process($attribute->getValue());

            $node->addAttribute(Attribute::fromArray($data));
        }
    }
}
