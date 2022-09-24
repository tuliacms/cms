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

INSERT INTO `#__content_type` (`code`, `type`, `name`, `icon`, `controller`, `is_routable`, `is_hierarchical`, `routing_strategy`) VALUES
('page', 'node', 'Page', 'fas fa-boxes', NULL, 1, 1, 'simple');
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
  `has_nonscalar_value` tinyint(1) NOT NULL DEFAULT '0',
  `position` smallint UNSIGNED DEFAULT '0',
  `parent` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `taxonomy` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `#__content_type_field` (`id`, `code`, `content_type_code`, `group_code`, `type`, `name`, `is_multilingual`, `has_nonscalar_value`, `position`, `parent`, `taxonomy`) VALUES
('90125db0-0c18-48a4-bfd4-c76d6c303c8f', 'content', 'page', 'section_1653933292398_398_4', 'tulia_editor', 'Content', 1, 0, 1, NULL, NULL);
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

INSERT INTO `#__content_type_field_constraint` (`id`, `field_id`, `code`) VALUES
('19843dce-6625-423e-93e4-2f922425599b', 'd6a896ca-f805-4d97-9905-ea7f4f708a2c', 'length'),
('3dc86fb3-c5bb-4635-9199-9162e835cb0b', 'd6a896ca-f805-4d97-9905-ea7f4f708a2c', 'required');
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__content_type_field_constraint_modificator` (
  `constraint_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `#__content_type_field_constraint_modificator` (`constraint_id`, `code`, `value`) VALUES
('19843dce-6625-423e-93e4-2f922425599b', 'min', '22'),
('19843dce-6625-423e-93e4-2f922425599b', 'max', '33');
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

INSERT INTO `#__content_type_field_group` (`content_type_code`, `code`, `name`, `section`, `interior`, `active`, `position`) VALUES
('page', 'section_1653933292398_398_4', 'New section...', 'main', NULL, 0, 1);
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
CREATE TABLE `#__node_term_relationship` (
  `node_id` char(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `term_id` char(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `taxonomy` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__term` (
  `id` char(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `is_root` tinyint(1) NOT NULL DEFAULT '0',
  `parent_id` char(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` smallint NOT NULL DEFAULT '0',
  `level` int UNSIGNED NOT NULL DEFAULT '0',
  `count` int UNSIGNED DEFAULT '0',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `visibility` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `#__term` (`id`, `type`, `is_root`, `parent_id`, `position`, `level`, `count`, `title`, `slug`, `visibility`) VALUES
('00000000-0000-0000-0000-000000000000', 'category', 1, NULL, 0, 0, 0, 'root', 'root', 1),
('217ae341-de24-4b61-8405-66ec416b4398', 'category', 0, '66435c83-584b-48ee-8115-db7aa23608b7', 1, 2, 0, 'Level 2', 'level-2', 1),
('21d4cd46-64ce-45c6-ad2b-ad6281250726', 'category', 0, '217ae341-de24-4b61-8405-66ec416b4398', 1, 3, 0, 'Level 3', 'level-3', 1),
('2e2f328e-72ff-48d2-b423-4936890f7d33', 'category', 0, 'b89c2db5-2675-4268-8fef-aa30099ab871', 1, 3, 0, 'Kategoria podrzędna', 'kategoria-podrzedna', 1),
('58ed4a0c-4965-4a54-81d7-bcc51e52463e', 'category', 0, '21d4cd46-64ce-45c6-ad2b-ad6281250726', 1, 4, 0, 'Level 4', 'level-4', 1),
('66435c83-584b-48ee-8115-db7aa23608b7', 'category', 0, '00000000-0000-0000-0000-000000000000', 1, 1, 0, 'Level 1', 'level-1', 1),
('9a4c4ca3-4c83-46c0-8471-f2d5f8b9ef2c', 'category', 0, '00000000-0000-0000-0000-000000000000', 2, 1, 0, 'Kategoria', 'kategoria', 1),
('a94d7e88-001a-45c1-99eb-c9dff24620b2', 'category', 0, '58ed4a0c-4965-4a54-81d7-bcc51e52463e', 1, 5, 0, 'Level 5', 'level-5', 1),
('b89c2db5-2675-4268-8fef-aa30099ab871', 'category', 0, '9a4c4ca3-4c83-46c0-8471-f2d5f8b9ef2c', 1, 2, 0, 'Kolejna kategoria', 'kolejna-kategoria', 1);
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__term_attribute` (
  `id` char(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `owner_id` char(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_renderable` tinyint(1) NOT NULL DEFAULT '0',
  `has_nonscalar_value` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `uri` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `compiled_value` longtext COLLATE utf8_unicode_ci,
  `payload` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__term_attribute_lang` (
  `attribute_id` char(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `compiled_value` longtext COLLATE utf8_unicode_ci,
  `payload` json DEFAULT NULL,
  `locale` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en_US'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__term_lang` (
  `term_id` char(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en_US',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `visibility` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__term_path` (
  `term_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `#__term_path` (`term_id`, `locale`, `path`) VALUES
('217ae341-de24-4b61-8405-66ec416b4398', 'en_US', '/level-1/level-2'),
('21d4cd46-64ce-45c6-ad2b-ad6281250726', 'en_US', '/level-1/level-2/level-3'),
('2e2f328e-72ff-48d2-b423-4936890f7d33', 'en_US', '/kategoria/kolejna-kategoria/kategoria-podrzedna'),
('58ed4a0c-4965-4a54-81d7-bcc51e52463e', 'en_US', '/level-1/level-2/level-3/level-4'),
('66435c83-584b-48ee-8115-db7aa23608b7', 'en_US', '/level-1'),
('9a4c4ca3-4c83-46c0-8471-f2d5f8b9ef2c', 'en_US', '/kategoria'),
('a94d7e88-001a-45c1-99eb-c9dff24620b2', 'en_US', '/level-1/level-2/level-3/level-4/level-5'),
('b89c2db5-2675-4268-8fef-aa30099ab871', 'en_US', '/kategoria/kolejna-kategoria');
EOF);
        $this->addSql(<<<EOF
ALTER TABLE `#__activity`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#__cache_pools`
  ADD PRIMARY KEY (`item_id`);

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

ALTER TABLE `#__model_change_history`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#__node_term_relationship`
  ADD PRIMARY KEY (`node_id`,`term_id`),
  ADD KEY `node_id` (`node_id`) USING BTREE,
  ADD KEY `term_id` (`term_id`) USING BTREE;

ALTER TABLE `#__term`
  ADD PRIMARY KEY (`id`),
  ADD KEY `website_id` (`type`),
  ADD KEY `fk_term_parent_id` (`parent_id`);

ALTER TABLE `#__term_attribute`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_88CBC8F8E2C35FC` (`owner_id`);

ALTER TABLE `#__term_attribute_lang`
  ADD KEY `IDX_C93142EEDC9EE959` (`attribute_id`);

ALTER TABLE `#__term_lang`
  ADD PRIMARY KEY (`term_id`,`locale`) USING BTREE,
  ADD KEY `search_slug` (`slug`(191),`locale`,`visibility`) USING BTREE;

ALTER TABLE `#__term_path`
  ADD PRIMARY KEY (`path`,`locale`) USING BTREE,
  ADD KEY `fk_term_path_term_id` (`term_id`);

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

ALTER TABLE `#__node_term_relationship`
  ADD CONSTRAINT `fk_node_term_relationship_node_id` FOREIGN KEY (`node_id`) REFERENCES `#__node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_node_term_relationship_term_id` FOREIGN KEY (`term_id`) REFERENCES `#__term` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__term`
  ADD CONSTRAINT `fk_term_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `#__term` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__term_attribute`
  ADD CONSTRAINT `fk_term_attribute_term_id` FOREIGN KEY (`owner_id`) REFERENCES `#__term` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__term_attribute_lang`
  ADD CONSTRAINT `fk_term_attribute_lang_term_attribute_id` FOREIGN KEY (`attribute_id`) REFERENCES `#__term_attribute` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__term_lang`
  ADD CONSTRAINT `fk_term_lang_term_id` FOREIGN KEY (`term_id`) REFERENCES `#__term` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__term_path`
  ADD CONSTRAINT `fk_term_path_term_id` FOREIGN KEY (`term_id`) REFERENCES `#__term` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOF
);
    }

    public function down(Schema $schema) : void
    {
    }
}
