<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Infrastructure\Persistence\WriteModel;

use Tulia\Cms\Options\Domain\WriteModel\OptionsStorageInterface;
use Tulia\Cms\Platform\Infrastructure\Persistence\Domain\AbstractLocalizableStorage;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalOptionsStorage extends AbstractLocalizableStorage implements OptionsStorageInterface
{
    public function __construct(private ConnectionInterface $connection)
    {
    }

    public function find(string $name, string $locale): ?array
    {
        $result = $this->connection->fetchAllAssociative(
            'SELECT
                tm.*,
                COALESCE(tl.locale, :locale) AS `locale`,
                COALESCE(tl.`value`, tm.`value`) AS `value`
            FROM #__option tm
            LEFT JOIN #__option_lang tl
                ON tm.id = tl.option_id AND tl.locale = :locale
            WHERE tm.name = :name
            LIMIT 1', [
            'name' => $name,
            'locale' => $locale,
        ], [
            'name' => \PDO::PARAM_STR,
            'locale' => \PDO::PARAM_STR,
        ]);

        return $result[0] ?? null;
    }

    public function findAllForWebsite(string $locale): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT
                tm.*,
                COALESCE(tl.locale, :locale) AS `locale`,
                COALESCE(tl.`value`, tm.`value`) AS `value`
            FROM #__option tm
            LEFT JOIN #__option_lang tl
                ON tm.id = tl.option_id AND tl.locale = :locale', [
            'locale'    => $locale,
        ], [
            'locale'    => \PDO::PARAM_STR,
        ]);
    }

    public function delete(array $option): void
    {
        $this->connection->delete('#__option', $option);
    }

    protected function updateMainRow(array $data, bool $foreignLocale): void
    {
        $mainTable = [];
        $mainTable['multilingual'] = $data['multilingual'] ? '1' : '0';
        $mainTable['autoload'] = $data['autoload'] ? '1' : '0';

        if ($foreignLocale === false || $this->isMultilingualOption($data['name']) === false) {
            $mainTable['value'] = $data['value'];
        }

        $this->connection->update('#__option', $mainTable, ['name' => $data['name'], 'website_id' => $data['website_id']]);
    }

    protected function insertMainRow(array $data): void
    {
        $mainTable = [];
        $mainTable['id'] = $data['id'];
        $mainTable['name'] = $data['name'];
        $mainTable['value'] = $data['value'];
        $mainTable['multilingual'] = $data['multilingual'] ? '1' : '0';
        $mainTable['autoload'] = $data['autoload'] ? '1' : '0';

        $this->connection->insert('#__option', $mainTable);
    }

    protected function insertLangRow(array $data): void
    {
        if ($this->isMultilingualOption($data['name']) === false) {
            return;
        }

        $langTable = [];
        $langTable['option_id'] = $data['id'];
        $langTable['locale'] = $data['locale'];
        $langTable['value'] = $data['value'];

        $this->connection->insert('#__option_lang', $langTable);
    }

    protected function updateLangRow(array $data): void
    {
        if ($this->isMultilingualOption($data['name']) === false) {
            return;
        }

        $langTable = [];
        $langTable['value'] = $data['value'];

        $this->connection->update('#__option_lang', $langTable, [
            'option_id' => $data['id'],
            'locale' => $data['locale'],
        ]);
    }

    protected function langExists(array $data): bool
    {
        $result = $this->connection->fetchAllAssociative(
            'SELECT option_id FROM #__option_lang WHERE option_id = :id AND locale = :locale LIMIT 1',
            ['id' => $data['id'], 'locale' => $data['locale']]
        );

        return isset($result[0]['option_id']) && $result[0]['option_id'] === $data['id'];
    }

    private function isMultilingualOption(string $name): bool
    {
        return (bool) $this->connection->createQueryBuilder()
            ->select('o.multilingual')
            ->from('#__option', 'o')
            ->andWhere('o.name = :name')
            ->setParameter('name', $name, \PDO::PARAM_STR)
            ->execute()
            ->fetchColumn();
    }
}
