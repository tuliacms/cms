<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\UserInterface\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
#[AsCommand(name: 'content-builder:content-type-entry:list')]
class ContentBuilderContentTypeEntryListCommand extends Command
{
    private ContentTypeRegistryInterface $contentTypeRegistry;

    public function __construct(ContentTypeRegistryInterface $contentTypeRegistry)
    {
        parent::__construct(static::$defaultName);

        $this->contentTypeRegistry = $contentTypeRegistry;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rows = [];

        foreach ($this->contentTypeRegistry->all() as $type) {
            $rows[] = [
                $type->getCode(),
                $type->getType(),
                $type->getName(),
                $type->isRoutable() ? 'Yes' : 'No',
                $type->isHierarchical() ? 'Yes' : 'No',
            ];
        }

        usort($rows, function ($a, $b) {
            return $a[0] <=> $b[0];
        });

        $io = new SymfonyStyle($input, $output);
        $io->title('List of Content Type Entries');

        (new Table($output))
            ->setHeaders(['Code', 'Type', 'Name', 'Routable', 'Hierarchical'])
            ->setRows($rows)
            ->render()
        ;

        return Command::SUCCESS;
    }
}
