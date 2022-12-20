<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormType;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
trait AttributesAwareFormTypeTrait
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('website');
        $resolver->setAllowedTypes('website', WebsiteInterface::class);

        $resolver->setRequired('content_type');
        $resolver->setAllowedTypes('content_type', 'string');

        $resolver->setDefault('partial_view', null);
        $resolver->setAllowedTypes('partial_view', ['null', 'string']);

        $resolver->setDefault('context', null);
        $resolver->setAllowedTypes('context', ['null', 'array']);
    }
}
