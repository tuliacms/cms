<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\Infrastructure\Cms\SearchAnything;

use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Cms\Options\Domain\WriteModel\OptionsRepositoryInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteRegistryInterface;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\AbstractDocumentCollector;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\IndexInterface;
use Tulia\Cms\Settings\Domain\Group\SettingsGroupRegistryInterface;
use Tulia\Cms\Website\Domain\WriteModel\WebsiteRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class SettingsDocumentCollector extends AbstractDocumentCollector
{
    public function __construct(
        /*private readonly SettingsGroupRegistryInterface $settings,
        private readonly OptionsRepositoryInterface $optionsRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly WebsiteRegistryInterface $websiteRegistry,*/
    ) {
    }

    public function collect(IndexInterface $index, ?string $websiteId, ?string $locale, int $offset, int $limit): void
    {
        /*foreach ($this->settings->all() as $group) {
            $group->setFormFactory($this->formFactory);
            $group->setOptionsRepository($this->optionsRepository);
            $group->setWebsite($this->websiteRegistry->get($websiteId));
            $form = $group->buildForm();

            dump($form);
        }*/
        /*foreach ($this->collector->collectDocumentsOfLocale($websiteId, $locale, $offset, $limit) as $node) {
            $document = $index->document($node['id'], $websiteId, $locale);
            $document->setLink('backend.node.edit', ['id' => $node['id'], 'node_type' => $node['type']]);
            $document->setTitle($node['title']);

            $index->save($document);
        }*/
    }

    public function countDocuments(string $websiteId, string $locale): int
    {
        /*dump($this->settings->all());exit;*/
        /*return $this->collector->countDocumentsOfLocale($websiteId, $locale);*/
    }
}
