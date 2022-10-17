<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\UserInterface\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\DocumentCollectorRegistryInterface;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\IndexerInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
#[AsCommand(name: 'search-anything:index')]
final class IndexCommand extends Command
{
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
        $defaultLocale = $this->translator->getLocale();

        foreach ($this->indexersRegistry->all() as $collector) {
            if ($collector->isMultilingual()) {
                foreach ($this->website->getLocales() as $locale) {
                    if ($this->translator instanceof LocaleAwareInterface) {
                        $this->translator->setLocale($locale->getCode());
                    }

                    $index = $this->indexer->index($collector->getIndex(), $this->website->getId(), $locale->getCode());
                    $index->clear();
                    $offset = 0;
                    $limit = 100;

                    do {
                        $delta = $index->getDelta();
                        $collector->collect($index, $this->website->getId(), $locale->getCode(), $offset, $limit);
                        $offset += $limit;
                    } while ($delta !== $index->getDelta());
                }
            } else {
                if ($this->translator instanceof LocaleAwareInterface) {
                    $this->translator->setLocale($defaultLocale);
                }

                $index = $this->indexer->index($collector->getIndex(), $this->website->getId(), 'unilingual');
                $index->clear();
                $offset = 0;
                $limit = 100;

                do {
                    $delta = $index->getDelta();
                    $collector->collect($index, $this->website->getId(), 'unilingual', $offset, $limit);
                    $offset += $limit;
                } while ($delta !== $index->getDelta());
            }
        }

        $output->writeln('Done.');
        return Command::SUCCESS;
    }
}
