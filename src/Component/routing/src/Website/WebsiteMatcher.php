<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website;

/**
 * @author Adam Banaszkiewicz
 */
final class WebsiteMatcher
{
    public static function matchAgainstRequest(array $websites, string $host, string $path): ?array
    {
        /**
         * Order by length to prevent situation, when we have two websites with similar path_prefix,
         * like: `/gardens` and `/gardens-vip`, and first one was created first. Matcher needs them
         * in proper order, so first we need to check the longest prefixes.
         */
        usort($websites, function ($a, $b) {
            return \strlen($b['path_prefix']) <=> \strlen($a['path_prefix']);
        });
        usort($websites, function ($a, $b) {
            return \strlen($b['locale_prefix']) <=> \strlen($a['locale_prefix']);
        });

        $prepared = [];

        foreach ($websites as $id => $website) {
            $prepared[] = array_merge($website, [
                'basepath' => $website['path_prefix'] . $website['locale_prefix'],
                'is_backend' => false,
            ]);
            $prepared[] = array_merge($website, [
                'basepath' => $website['path_prefix'] . $website['backend_prefix'] . $website['locale_prefix'],
                'is_backend' => true,
            ]);
        }

        usort($prepared, function ($a, $b) {
            return \strlen($b['basepath']) <=> \strlen($a['basepath']);
        });

        $activeWebsite = null;

        /**
         * First search for every website, that contains path prefix.
         * To prevent select website with matched domain, but empty path prefix.
         */
        foreach ($prepared as $website) {
            if (
                $website['domain'] === $host
                && !empty($website['basepath'])
                && strpos($path, $website['basepath']) === 0
            ) {
                $activeWebsite = $website;
                break;
            }
        }

        /**
         * If none of websites with path prefixes were matched,
         * now we want to match websites only by domain.
         */
        if (! $activeWebsite) {
            foreach ($prepared as $website) {
                if (
                    empty($website['basepath'])
                    && $website['domain'] === $host
                ) {
                    $activeWebsite = $website;
                    break;
                }
            }
        }

        return $activeWebsite;
    }
}
