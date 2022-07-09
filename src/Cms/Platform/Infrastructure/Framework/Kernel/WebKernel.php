<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Kernel;

use Tulia\Component\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class WebKernel extends AbstractKernel
{
    private WebsiteInterface $website;

    public function __construct(string $environment, bool $debug, WebsiteInterface $website)
    {
        parent::__construct($environment, $debug);
        $this->website = $website;
    }

    protected function initializeContainer(): void
    {
        parent::initializeContainer();

        $this->container->set(WebsiteInterface::class, $this->website);
    }
}
