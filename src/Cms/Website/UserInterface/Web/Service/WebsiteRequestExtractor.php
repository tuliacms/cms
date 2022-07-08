<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UserInterface\Web\Service;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Shared\Domain\WriteModel\UuidGeneratorInterface;
use Tulia\Component\Routing\Enum\SslModeEnum;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteRequestExtractor
{
    protected UuidGeneratorInterface $uuidGenerator;

    public function __construct(UuidGeneratorInterface $uuidGenerator)
    {
        $this->uuidGenerator = $uuidGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function extractFromRequest(Request $request, array $data = []): array
    {
        return array_merge($data, [
            'id' => $this->uuidGenerator->generate(),
            'backend_prefix' => '/administrator',
            'locales' => [
                [
                    'code' => $request->getPreferredLanguage(),
                    'domain' => $request->getHttpHost(),
                    'domainDevelopment' => $request->getHttpHost(),
                    'sslMode' => SslModeEnum::ALLOWED_BOTH,
                    'isDefault' => true,
                ]
            ]
        ]);
    }
}
