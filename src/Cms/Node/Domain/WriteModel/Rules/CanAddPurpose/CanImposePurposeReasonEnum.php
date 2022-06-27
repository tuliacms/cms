<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose;

/**
 * @author Adam Banaszkiewicz
 */
enum CanImposePurposeReasonEnum: string
{
    case PurposeDoesntExists = 'Purpose doesn\'t exists';
    case ThisSingularPurposeIsImposedToAnotherNode = 'This singular purpose is imposed to another node';
    case PurposeAlreadyImposedOnNode = 'Purpose already imposed on node';
    case OK = 'OK';
}
