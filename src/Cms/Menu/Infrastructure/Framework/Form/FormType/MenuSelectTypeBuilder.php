<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeBuilder\AbstractFieldTypeBuilder;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderScopeEnum;

/**
 * @author Adam Banaszkiewicz
 */
class MenuSelectTypeBuilder extends AbstractFieldTypeBuilder
{
    private MenuFinderInterface $menuFinder;

    public function __construct(MenuFinderInterface $menuFinder)
    {
        $this->menuFinder = $menuFinder;
    }

    public function buildOptions(Field $field, array $options, ContentType $contentType): array
    {
        $source = $this->menuFinder->find([], MenuFinderScopeEnum::INTERNAL);
        $menus = [];

        foreach ($source as $item) {
            $menus[$item->getName()] = $item->getId();
        }

        $options['choices'] = $menus;
        $options['choice_translation_domain'] = false;
        $options['constraints'][] = new Assert\Uuid();
        $options['constraints'][] = new Assert\NotBlank();
        $options['constraints'][] = new Assert\Choice([ 'choices' => $menus ]);

        return $options;
    }
}
