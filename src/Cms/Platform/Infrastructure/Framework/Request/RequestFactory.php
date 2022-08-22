<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Request;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Component\Routing\Website\WebsiteProvider;

/**
 * @author Adam Banaszkiewicz
 */
final class RequestFactory
{
    public static function factory($query, $request, $attributes, $cookies, $files, $server, $content) {
        $configFilename = __TULIA_PROJECT_DIR.'/config/dynamic.php';
        assert(file_exists($configFilename), 'Tulia CMS seems to be not installed. Please call make setup do initialize system.');

        $website = WebsiteProvider::provideByHostAndPath(
            $configFilename,
            $server['HTTP_HOST'],
            $server['REQUEST_URI'],
            $_SERVER['APP_ENV'] === 'dev'
        );

        $attributes['website'] = $website;

        if ($website->isBackend()) {
            $server['REQUEST_URI'] = str_replace($website->getBasepath(), $website->getBackendPrefix(), $server['REQUEST_URI']);
        } else {
            $server['REQUEST_URI'] = str_replace($website->getBasepath(), '', $server['REQUEST_URI']);
        }

        $server['TULIA_WEBSITE_ID'] = $website->getId();
        $server['TULIA_WEBSITE_IS_BACKEND'] = $website->isBackend();
        $server['TULIA_WEBSITE_BASEPATH'] = $website->getBasepath();
        $server['TULIA_WEBSITE_BACKEND_PREFIX'] = $website->getBackendPrefix();
        $server['TULIA_WEBSITE_LOCALE'] = $website->getLocale()->getCode();
        $server['TULIA_WEBSITE_LOCALE_DEFAULT'] = $website->getDefaultLocale()->getCode();
        $server['TULIA_WEBSITE_LOCALE_PREFIX'] = $website->getLocale()->getLocalePrefix();
        $server['TULIA_WEBSITE_PATH_PREFIX'] = $website->getLocale()->getPathPrefix();

        return new Request($query, $request, $attributes, $cookies, $files, $server, $content);
    }
}
