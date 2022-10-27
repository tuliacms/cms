<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\Persistence\Domain\WriteModel;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Website\Domain\WriteModel\Service\TranslationCopyMachine\CopyBusInterface;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class DbalContactFormTranslationCopyBus implements CopyBusInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function count(string $websiteId, string $defaultLocale): int
    {
        return (int) $this->connection->fetchOne('
            SELECT COUNT(ft.id)
            FROM #__form_translation AS ft
            INNER JOIN #__form AS f
                ON f.id = ft.form_id
            WHERE f.website_id = :website AND ft.locale = :locale', [
            'website' => Uuid::fromString($websiteId)->toBinary(),
            'locale' => $defaultLocale,
        ]);
    }

    public function copy(string $websiteId, string $defaultLocale, string $targetLocale, int $offset, int $limit): int
    {
        $idList = $this->getFormIdListPart($websiteId, $offset, $limit);
        $source = $this->getTranslationsSource($idList, $defaultLocale);

        $fields = $this->collectFields(array_column($source, 'id'));

        foreach ($source as $item) {
            $id = Uuid::v4()->toBinary();

            $this->connection->insert('#__form_translation', [
                'id' => $id,
                'form_id' => $item['form_id'],
                'locale' => $targetLocale,
                'subject' => $item['subject'],
                'message_template' => $item['message_template'],
                'fields_view' => $item['fields_view'],
                'fields_template' => $item['fields_template'],
                'translated' => 0,
            ]);

            foreach ($fields[$item['id']] ?? [] as $field) {
                $this->connection->insert('#__form_field_translation', [
                    'id' => Uuid::v4()->toBinary(),
                    'translation_id' => $id,
                    'locale' => $targetLocale,
                    'name' => $field['name'],
                    'type' => $field['type'],
                    'options' => $field['options'],
                ]);
            }
        }

        return count($source);
    }

    public function delete(string $websiteId, string $defaultLocale, string $targetLocale, int $offset, int $limit): int
    {
        $idList = $this->getFormIdListPart($websiteId, $offset, $limit);
        $translationsIdList = array_column($this->getTranslationsSource($idList, $targetLocale), 'id');

        $this->connection->executeStatement(
            'DELETE FROM #__form_field_translation WHERE translation_id IN (:id)',
            ['id' => $translationsIdList],
            ['id' => Connection::PARAM_STR_ARRAY]
        );
        $this->connection->executeStatement(
            'DELETE FROM #__form_translation WHERE id IN (:id)',
            ['id' => $translationsIdList],
            ['id' => Connection::PARAM_STR_ARRAY]
        );

        return count($idList);
    }

    private function collectFields(array $idList): array
    {
        $source = $this->connection->fetchAllAssociative('
            SELECT translation_id, name, type, locale, options
            FROM #__form_field_translation
            WHERE translation_id IN (:idList)
        ', [
            'idList' => $idList,
        ], [
            'idList' => Connection::PARAM_STR_ARRAY,
        ]);
        $result = [];

        foreach ($source as $item) {
            $result[$item['translation_id']][] = $item;
        }

        return $result;
    }

    private function getFormIdListPart(string $websiteId, int $offset, int $limit): array
    {
        return $this->connection->fetchFirstColumn('
            SELECT id
            FROM #__form
            WHERE website_id = :website
            ORDER BY id ASC
            LIMIT :offset, :limit', [
            'website' => Uuid::fromString($websiteId)->toBinary(),
            'offset' => $offset,
            'limit' => $limit,
        ], [
            'offset' => \PDO::PARAM_INT,
            'limit' => \PDO::PARAM_INT,
        ]);
    }

    private function getTranslationsSource(array $formIdList, string $locale): array
    {
        return $this->connection->fetchAllAssociative('
            SELECT id, form_id, locale, subject, message_template, fields_view, fields_template
            FROM #__form_translation
            WHERE form_id IN (:ids) AND locale = :locale', [
            'locale' => $locale,
            'ids' => $formIdList,
        ], [
            'ids' => Connection::PARAM_STR_ARRAY,
        ]);
    }
}
