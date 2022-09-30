<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Application\UseCase;

use Doctrine\DBAL\Connection;
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
use Tulia\Cms\Website\Application\UseCase\UpdateWebsite;
use Tulia\Cms\Website\Application\UseCase\UpdateWebsiteRequest;
use Tulia\Component\Importer\ImporterInterface;
use Tulia\Component\Routing\Website\WebsiteRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class SetupSystem extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly string $rootDir,
        private readonly string $cmsCoreDir,
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly CreateUser $createUser,
        private readonly WebsitesOptionsRegistrator $optionsRegistrator,
        private readonly OptionsRepositoryInterface $optionsRepository,
        private readonly UpdateWebsite $updateWebsite,
        private readonly Connection $connection,
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
        $websiteId = $this->getWebsiteId();
        $this->updateOptions($websiteId, $request->websiteLocale, $request->username);
        $this->updateWebsite($websiteId, $request->websiteName, $request->websiteLocale, $request->websiteLocalDomain, $request->websiteProductionDomain);
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

    private function updateWebsite(string $websiteId, string $name, string $locale, string $localDomain, string $productionDomain): void
    {
        ($this->updateWebsite)(new UpdateWebsiteRequest(
            $websiteId,
            $name,
            true,
            [[
                'code' => $locale,
                'domain' => $productionDomain,
                'domain_development' => $localDomain,
            ]]
        ));
    }

    private function createAdminUser(string $username, string $password): string
    {
        $this->connection->executeQuery('DELETE FROM #__user');

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

    private function getWebsiteId(): string
    {
        return $this->connection->fetchOne('SELECT BIN_TO_UUID(id) AS id from #__website');
    }

    private function updateTheme(string $websiteId): void
    {
        $this->themeActivator->activateTheme('Tulia/Lisa', $websiteId);
    }

    private function importSampleData(string $websiteId, string $authorId): void
    {
        $this->importer->importFromFile(
            $this->cmsCoreDir.'/Cms/Platform/Infrastructure/Framework/Resources/imports/sample-website-data.json',
            parameters: [
                'website' => $this->websiteRegistry->get($websiteId),
                'author_id' => $authorId,
            ]
        );
    }
}