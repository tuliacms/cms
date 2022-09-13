<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\UserInterface\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ConstraintTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
#[AsCommand(name: 'content-builder:constraint-type:list')]
class ContentBuilderConstraintTypeListCommand extends Command
{
    private ConstraintTypeMappingRegistry $mappingRegistry;

    public function __construct(ConstraintTypeMappingRegistry $mappingRegistry)
    {
        parent::__construct(static::$defaultName);

        $this->mappingRegistry = $mappingRegistry;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rows = [];

        foreach ($this->mappingRegistry->all() as $code => $type) {
            $rows[] = [
                $code,
                $type['classname'],
                $type['label'],
                implode(', ', array_keys($type['modificators'])),
            ];
        }

        (new Table($output))
            ->setHeaders(['code', 'classname', 'label', 'modificators'])
            ->setRows($rows)
            ->render()
        ;

        return Command::SUCCESS;
    }
}
