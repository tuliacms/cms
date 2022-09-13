<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\UserInterface\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Service\Configuration;

/**
 * @author Adam Banaszkiewicz
 */
#[AsCommand(name: 'content-builder:content-type:list')]
class ContentBuilderContentTypeListCommand extends Command
{
    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        parent::__construct(static::$defaultName);

        $this->configuration = $configuration;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rows = [];

        foreach ($this->configuration->getTypes() as $type) {
            $rows[] = [
                $type,
                $this->configuration->isConfigurable($type) ? 'Yes' : 'No',
                $this->configuration->isMultilingual($type) ? 'Yes' : 'No',
                $this->configuration->getController($type),
                $this->configuration->getLayoutBuilder($type),
            ];
        }

        (new Table($output))
            ->setHeaders(['code', 'configurable', 'multilingual', 'controller', 'layout_builder'])
            ->setRows($rows)
            ->render()
        ;

        return Command::SUCCESS;
    }
}
