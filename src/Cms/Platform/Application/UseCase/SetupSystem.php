<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Application\UseCase;

use Tulia\Cms\Options\Application\Service\WebsitesOptionsRegistrator;
use Tulia\Cms\Options\Domain\WriteModel\OptionsRepositoryInterface;
use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Domain\WriteModel\UuidGeneratorInterface;
use Tulia\Cms\Theme\Application\Service\ThemeActivator;
use Tulia\Cms\User\Application\UseCase\CreateUser;
use Tulia\Cms\User\Application\UseCase\CreateUserRequest;
use Tulia\Cms\Website\Application\UseCase\CreateWebsite;
use Tulia\Cms\Website\Application\UseCase\CreateWebsiteRequest;
use Tulia\Component\Importer\ImporterInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class SetupSystem extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly string $rootDir,
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly CreateUser $createUser,
        private readonly WebsitesOptionsRegistrator $optionsRegistrator,
        private readonly OptionsRepositoryInterface $optionsRepository,
        private readonly CreateWebsite $createWebsite,
        private readonly ThemeActivator $themeActivator,
        private readonly ImporterInterface $importer,
        private readonly WebsiteRegistryInterface $websiteRegistry,
    ) {
    }

    /**
     * @param RequestInterface&SetupSystemRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $websiteId = $this->createWebsite($request->websiteName, $request->websiteLocale, $request->websiteLocalDomain, $request->websiteProductionDomain);
        $this->updateOptions($websiteId, $request->websiteLocale, $request->username);
        $this->updateTheme($websiteId);
        $userId = $this->createAdminUser($request->username, $request->userPassword);

        if ($request->installSampleData) {
            $this->importSampleData($websiteId, $userId);
        }

        if (!($_ENV['APP_SECRET'] ?? null)) {
            $secret = $this->uuidGenerator->generate();
            $this->appendToDotenv('APP_SECRET', $secret);
        }

        return null;
    }

    private function appendToDotenv(string $variable, string $value): void
    {
        $lines = file($this->rootDir.'/.env');
        $found = false;

        foreach ($lines as $no => $line) {
            if (strpos($line, "$variable=") === 0) {
                $lines[$no] = "$variable=$value\n";
                $found = true;
            }
        }

        if ($found === false) {
            $lines[] = "$variable=$value\n";
        }

        file_put_contents($this->rootDir.'/.env', implode('', $lines));
    }

    private function createWebsite(string $name, string $locale, string $domainDevelopment, string $domain): string
    {
        /** @var IdResult $result */
        $result = ($this->createWebsite)(new CreateWebsiteRequest(
            name: $name,
            enabled: true,
            locale: $locale,
            domain: $domain,
            domainDevelopment: $domainDevelopment,
        ));

        return $result->id;
    }

    private function createAdminUser(string $username, string $password): string
    {
        /** @var IdResult $result */
        $result = ($this->createUser)(new CreateUserRequest(
            $username,
            $password,
            ['ROLE_ADMIN']
        ));

        return $result->id;
    }

    private function updateOptions(string $websiteId, string $locale, string $username): void
    {
        $this->optionsRegistrator->registerMissingOptions($websiteId);

        $option = $this->optionsRepository->get('administrator_email', $websiteId);
        $option->setValue($username, $locale, $locale);
        $this->optionsRepository->save($option);
    }

    private function updateTheme(string $websiteId): void
    {
        $this->themeActivator->activateTheme('Tulia/Lisa', $websiteId);
    }

    private function importSampleData(string $websiteId, string $authorId): void
    {
        $this->importer->importFromFile(
            $this->rootDir.'/extension/theme/Tulia/Lisa/Resources/imports/sample-website-data.json',
            parameters: [
                'website' => $this->websiteRegistry->get($websiteId),
                'author_id' => $authorId,
            ]
        );
    }
}
