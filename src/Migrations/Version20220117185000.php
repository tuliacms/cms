<?php

declare(strict_types=1);

namespace Tulia\Cms\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @author Adam Banaszkiewicz
 */
final class Version20220117185000 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql(<<<EOF
CREATE TABLE `#__activity` (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `translation_domain` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'messages',
  `context` json NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__content_type` (
  `code` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `controller` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_routable` tinyint(1) NOT NULL DEFAULT '0',
  `is_hierarchical` tinyint(1) NOT NULL DEFAULT '0',
  `routing_strategy` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__content_type_field` (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `content_type_code` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `group_code` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `is_multilingual` tinyint(1) NOT NULL DEFAULT '0',
  `position` smallint UNSIGNED DEFAULT '0',
  `parent` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `taxonomy` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__content_type_field_configuration` (
  `field_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__content_type_field_constraint` (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `field_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__content_type_field_constraint_modificator` (
  `constraint_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__content_type_field_group` (
  `content_type_code` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `section` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `interior` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `position` smallint UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);
       /* $this->addSql(<<<EOF
CREATE TABLE `#__model_change_history` (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `module` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `model` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `identity` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `occured_on` datetime NOT NULL,
  `done_by` enum('USER','SYSTEM','CLI') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `author_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `author_name` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('CREATE','UPDATE','DELETE') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `property_name` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `value_before` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `value_after` text CHARACTER SET utf8 COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);*/
        $this->addSql(<<<EOF
ALTER TABLE `#__activity`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#__content_type`
  ADD UNIQUE KEY `code` (`code`);

ALTER TABLE `#__content_type_field`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_content_type_field_content_type_code` (`content_type_code`);

ALTER TABLE `#__content_type_field_configuration`
  ADD KEY `node_type_field_configuration_field_id` (`field_id`);

ALTER TABLE `#__content_type_field_constraint`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_node_type_field_constraint_field_id` (`field_id`);

ALTER TABLE `#__content_type_field_constraint_modificator`
  ADD KEY `fk_node_type_field_constraint_modificator_constraint_id` (`constraint_id`);

ALTER TABLE `#__content_type_field_group`
  ADD KEY `fk_content_type_field_group_content_type_code` (`content_type_code`);

ALTER TABLE `#__customizer_changeset`
  ADD PRIMARY KEY (`id`),
  ADD KEY `theme` (`type`,`theme`) USING BTREE;

ALTER TABLE `#__customizer_changeset_lang`
  ADD UNIQUE KEY `customizer_changeset_id` (`customizer_changeset_id`,`locale`);

-- ALTER TABLE `#__model_change_history`
--   ADD PRIMARY KEY (`id`);

ALTER TABLE `#__content_type_field`
  ADD CONSTRAINT `fk_content_type_field_content_type_code` FOREIGN KEY (`content_type_code`) REFERENCES `#__content_type` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__content_type_field_configuration`
  ADD CONSTRAINT `node_type_field_configuration_field_id` FOREIGN KEY (`field_id`) REFERENCES `#__content_type_field` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__content_type_field_constraint`
  ADD CONSTRAINT `fk_node_type_field_constraint_field_id` FOREIGN KEY (`field_id`) REFERENCES `#__content_type_field` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__content_type_field_constraint_modificator`
  ADD CONSTRAINT `fk_node_type_field_constraint_modificator_constraint_id` FOREIGN KEY (`constraint_id`) REFERENCES `#__content_type_field_constraint` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__content_type_field_group`
  ADD CONSTRAINT `fk_content_type_field_group_content_type_code` FOREIGN KEY (`content_type_code`) REFERENCES `#__content_type` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__customizer_changeset_lang`
  ADD CONSTRAINT `customizer_changeset_lang_ibfk_1` FOREIGN KEY (`customizer_changeset_id`) REFERENCES `#__customizer_changeset` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOF
);
    }

    public function down(Schema $schema) : void
    {
    }
}
