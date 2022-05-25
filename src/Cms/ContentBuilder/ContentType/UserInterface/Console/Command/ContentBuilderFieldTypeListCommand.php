<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\ContentType\UserInterface\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tulia\Cms\ContentBuilder\Layout\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class ContentBuilderFieldTypeListCommand extends Command
{
    protected static $defaultName = 'content-builder:field-type:list';
    private FieldTypeMappingRegistry $mappingRegistry;

    public function __construct(FieldTypeMappingRegistry $mappingRegistry)
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
                $type['label'],
                $type['classname'],
                implode(', ', $type['only_for_types']),
                implode(', ', $type['exclude_for_types']),
            ];
        }

        (new Table($output))
            ->setHeaders(['code', 'label', 'classname', 'only_for_types', 'exclude_for_types'])
            ->setRows($rows)
            ->render()
        ;

        return Command::SUCCESS;
    }
}
