<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\UserInterface\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
#[AsCommand(name: 'content-builder:content-type-entry:info')]
class ContentBuilderContentTypeEntryInfoCommand extends Command
{
    private ContentTypeRegistryInterface $contentTypeRegistry;

    public function __construct(ContentTypeRegistryInterface $contentTypeRegistry)
    {
        parent::__construct(static::$defaultName);

        $this->contentTypeRegistry = $contentTypeRegistry;
    }

    protected function configure()
    {
        $this->addArgument('code', InputArgument::REQUIRED, 'Field Type to debug');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($this->contentTypeRegistry->has($input->getArgument('code')) === false) {
            $io->warning(sprintf('Content Type Entry "%s" not exists.', $input->getArgument('code')));
            return Command::FAILURE;
        }

        $type = $this->contentTypeRegistry->get($input->getArgument('code'));

        $io->title(sprintf('Informations about "%s" Content Type Entry', $input->getArgument('code')));

        $io->definitionList(
            ['Code' => $input->getArgument('code')],
            ['Content Type Code' => $type->getType()],
            ['Name' => $type->getName()],
            ['Icon' => $type->getIcon() ? $type->getIcon() : "''"],
            ['Controller' => $type->getController() ? $type->getController() : "''"],
            ['Is routable' => $type->isRoutable() ? 'Yes' : 'No'],
            ['Is hierarchical' => $type->isHierarchical() ? 'Yes' : 'No'],
            ['Routing strategy' => $type->getRoutingStrategy() ? $type->getRoutingStrategy() : "''"],
        );

        return Command::SUCCESS;
    }
}
