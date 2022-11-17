<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\Infrastructure\Cms\SearchAnything;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\AbstractDocumentCollector;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\IndexInterface;
use Tulia\Cms\Settings\Domain\Group\SettingsGroupRegistryInterface;
use Tulia\Cms\Settings\Domain\Group\SettingsStorage;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class SettingsDocumentCollector extends AbstractDocumentCollector
{
    public function __construct(
        private readonly SettingsGroupRegistryInterface $settings,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function collect(IndexInterface $index, ?string $websiteId, ?string $locale, int $offset, int $limit): void
    {
        // No pagination in this collector. Next pages should be empty.
        if ($offset !== 0) {
            return;
        }

        foreach ($this->settings->all() as $group) {
            $group->setFormFactory($this->formFactory);
            $form = $group->buildForm(new SettingsStorage([]));

            /** @var \Symfony\Component\Form\Form $field */
            foreach ($form as $field) {
                $options = $field->getConfig()->getOptions();

                $document = $index->document(sprintf('%s.%s', $group->getId(), $field->getName()), $websiteId, $locale);
                $document->setLink('backend.settings', ['group' => $group->getId()]);
                $document->setTitle($this->translator->trans($options['label'], [], $options['translation_domain']));

                $index->save($document);
            }
        }
    }

    public function countDocuments(string $websiteId, string $locale): int
    {
        // Return hardcoded, because this method is used in langauges managing.
        // In this case, settings from forms are not performance-weak.
        return 1;
    }
}
