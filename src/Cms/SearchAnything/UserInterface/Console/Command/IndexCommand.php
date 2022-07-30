<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\UserInterface\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\DocumentCollectorRegistryInterface;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\IndexerInterface;
use Tulia\Component\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class IndexCommand extends Command
{
    protected static $defaultName = 'search-anything:index';

    public function __construct(
        private DocumentCollectorRegistryInterface $indexersRegistry,
        private IndexerInterface $indexer,
        private WebsiteInterface $website,
        private TranslatorInterface $translator
    ) {
        parent::__construct(static::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->indexersRegistry->all() as $collector) {
            foreach ($this->website->getLocales() as $locale) {
                if ($this->translator instanceof LocaleAwareInterface) {
                    $this->translator->setLocale($locale->getCode());
                }

                $index = $this->indexer->index($collector->getIndex(), $locale->getCode());
                $index->clear();
                $offset = 0;
                $limit = 100;

                do {
                    $delta = $index->getDelta();
                    $collector->collect($index, $locale->getCode(), $offset, $limit);
                    $offset += $limit;
                } while ($delta !== $index->getDelta());
            }
        }

        $output->writeln('Done.');
        return Command::SUCCESS;
    }
}
