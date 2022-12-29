<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Doctrine\Dbal\ReadModel;

/**
 * @author Adam Banaszkiewicz
 */
final class NodeFinderCache
{
    private array $termsCache = [];

    public function fetchTermsUntilExists(array $nodeIdList, \Closure $closure): array
    {
        $result = [];

        foreach ($nodeIdList as $key => $id) {
            if (isset($this->termsCache[$id])) {
                $result[$id] = $this->termsCache[$id];
                unset($nodeIdList[$key]);
            }
        }

        if (empty($nodeIdList)) {
            return $result;
        }

        $result += $closure($nodeIdList);

        foreach ($result as $nodeId => $val) {
            $this->termsCache[$nodeId] = $val;
        }

        return $result;
    }
}
