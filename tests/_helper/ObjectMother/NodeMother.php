<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Helper\ObjectMother;

use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\Service\ParentTermsResolverInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class NodeMother
{
    private string $websiteId = 'ceb7a799-9491-4880-a50e-0c7f0dcba7f5';
    private string $author = '9e8dc655-f476-4838-8c80-b22b1d401aab';
    private array $availableLocales = ['en_US', 'pl_PL'];
    private array $assignedToTerms = [];

    private function __construct(
        private string $title,
        private string $type,
    ) {
    }

    public static function aNode(string $title, string $type = 'page'): self
    {
        return new self($title, $type);
    }

    public function withAssignationToTermOfTaxonomy(string $term, string $taxonomy): self
    {
        $this->assignedToTerms[] = [$term, $taxonomy];
        return $this;
    }

    public function build(
        ?ParentTermsResolverInterface $resolver = null,
    ): Node {
        $node = Node::create(
            $this->title,
            $this->type,
            $this->websiteId,
            $this->author,
            $this->title,
            $this->availableLocales,
        );

        if ($this->assignedToTerms !== []) {
            foreach ($this->assignedToTerms as $assignation) {
                $node->assignToTermOf($resolver, $assignation[0], $assignation[1]);
            }
        }

        $node->collectDomainEvents();

        return $node;
    }
}
