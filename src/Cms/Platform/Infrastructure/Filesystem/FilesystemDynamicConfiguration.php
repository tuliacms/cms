<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Filesystem;

use Tulia\Cms\Platform\Domain\Service\DynamicConfigurationInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class FilesystemDynamicConfiguration implements DynamicConfigurationInterface
{
    private bool $opened = false;

    public function __construct(
        private readonly string $configFilename,
        private readonly string $environment,
        private array $config,
    ) {
    }

    public function set(string $key, float|int|array|string|null $value): void
    {
        $this->open();

        $this->config[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->open();

        return $this->config[$key] ?? $default;
    }

    public function remove(string $key): void
    {
        unset($this->config[$key]);
    }

    public function write(): void
    {
        if ($this->opened === false) {
            throw new \RuntimeException('Cannot write not opened configuration.');
        }

        file_put_contents(
            $this->configFilename,
            sprintf('<?php return %s;', var_export($this->config, true))
        );

        $this->opened = false;
    }

    public function open(): void
    {
        if ($this->environment === 'prod' || $this->opened) {
            return;
        }

        if (is_file($this->configFilename) && is_writable($this->configFilename) === false) {
            throw new \RuntimeException('Dynamic configuration file is not writable.');
        }

        $this->config = include $this->configFilename;
        $this->opened = true;
    }
}
