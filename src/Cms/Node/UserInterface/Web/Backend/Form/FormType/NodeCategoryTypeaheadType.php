<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Backend\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Tulia\Cms\Taxonomy\UserInterface\Web\Shared\Form\FormType\TaxonomyTypeaheadType;

/**
 * @author Adam Banaszkiewicz
 */
final class NodeCategoryTypeaheadType extends AbstractType
{
    public function __construct()
    {
    }

    public function getParent(): string
    {
        return TaxonomyTypeaheadType::class;
    }
}
