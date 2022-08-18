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

INSERT INTO `#__activity` (`id`, `message`, `translation_domain`, `context`, `created_at`) VALUES
('b2e0caa5-f533-49e3-83de-b5fd575090a2', 'activityUserCreatedNewNode', 'node', '{\"link\": \"/administrator/node/faq/edit/bf668e12-be10-4dd8-a397-f9368c312fcf\", \"username\": \"<a href=\\\"/administrator/user/edit/0167ee34-f63d-4252-b87b-66245aa45b7e\\\">Adam Banaszkiewicz</a>\"}', '2021-04-28 06:07:43'),
('b7a81af9-4252-4545-9a9e-79664d23d0bf', 'activityUserCreatedNewNode', 'node', '{\"link\": \"/administrator/node/page/edit/5b2f11c8-0a5a-4038-8f86-d9705733ffbe\", \"username\": \"<a href=\\\"/administrator/user/edit/0167ee34-f63d-4252-b87b-66245aa45b7e\\\">Adam Banaszkiewicz</a>\"}', '2021-05-02 13:17:02'),
('eb3d1f56-569e-44b4-b356-38d76f3d845a', 'activityUserCreatedNewNode', 'node', '{\"link\": \"/administrator/node/page/edit/d1b6fd2d-a8f2-4b59-8f72-94637697c121\", \"username\": \"<a href=\\\"/administrator/user/edit/0167ee34-f63d-4252-b87b-66245aa45b7e\\\">Adam Banaszkiewicz</a>\"}', '2021-04-26 06:03:12');
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
('3af23162-4a76-41f5-961a-59a981d0bd44', 'category', 'page', 'section_1653933292398_398_4', 'taxonomy', 'Category', 0, 0, 1, NULL, NULL),
('51e6057c-6fb7-4e23-bde1-2edc9114a9fb', 'text_3', 'page', 'section_1654934770009_9_6', 'text', 'text 3', 0, 0, 1, NULL, NULL),
('54221c16-7312-47f8-a8e1-700b696453d8', 'select', 'page', 'section_1653933292398_398_4', 'select', 'Select', 0, 0, 3, NULL, NULL),
('c83714dd-c0a3-4219-8c3c-ee204582044a', 'text_2', 'page', 'section_1654934769147_147_4', 'text', 'text 2', 0, 0, 1, NULL, NULL),
('c8777809-dd08-4c34-85f9-5f28a3377d6e', 'content', 'page', 'section_1653933292398_398_4', 'tulia_editor', 'Content', 1, 0, 4, NULL, NULL),
('d6a896ca-f805-4d97-9905-ea7f4f708a2c', 'textline', 'page', 'section_1653933292398_398_4', 'text', 'Textline', 1, 0, 2, NULL, NULL),
('e44e5139-19f8-44a8-aeeb-cccc7734dee7', 'text_1', 'page', 'section_1654934769577_577_5', 'text', 'text 1', 0, 0, 1, NULL, NULL);
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__content_type_field_configuration` (
  `field_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `#__content_type_field_configuration` (`field_id`, `code`, `value`) VALUES
('3af23162-4a76-41f5-961a-59a981d0bd44', 'taxonomy', 'category'),
('54221c16-7312-47f8-a8e1-700b696453d8', 'choices', 'asdas'),
('54221c16-7312-47f8-a8e1-700b696453d8', 'multiple', '1'),
('54221c16-7312-47f8-a8e1-700b696453d8', 'placeholder', 'asdasd');
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
('page', 'section_1654934769577_577_5', 'New section... 2', 'sidebar', NULL, 0, 1),
('page', 'section_1654934769147_147_4', 'New section... 1', 'sidebar', NULL, 0, 2),
('page', 'section_1654934770009_9_6', 'New section... 3', 'sidebar', NULL, 0, 3),
('page', 'section_1653933292398_398_4', 'New section...', 'main', NULL, 0, 4);
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__customizer_changeset` (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `theme` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `author_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `payload` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `#__customizer_changeset` (`id`, `theme`, `type`, `created_at`, `updated_at`, `author_id`, `payload`) VALUES
('2c0bc11b-9733-4877-a50d-4413f4de33e6', 'Tulia/Lisa', 'temporary', '2022-02-28 18:21:59', '2022-02-28 18:23:59', NULL, '{\"hero.static.link\": \"\", \"hero.static.headline\": \"\", \"hero.static.background\": \"\", \"hero.static.button.show\": \"0\", \"hero.static.description\": \"\", \"lisa.layout.breadcrumbs\": \"show\", \"hero.static.button.label\": \"\", \"lisa.footer.contact.type\": \"0\", \"lisa.footer.contact.email\": \"\", \"lisa.footer.contact.phone\": \"\", \"lisa.footer.contact.address\": \"\", \"lisa.footer.socials.twitter\": \"\", \"lisa.footer.socials.youtube\": \"\", \"lisa.footer.socials.facebook\": \"\", \"lisa.footer.contact.copyrights\": \"Tulia CMS\"}'),
('e890167e-fb55-4508-8486-3eaf54a164b9', 'Tulia/Lisa', 'active', '2022-03-04 07:10:19', NULL, '', '{\"hero.static.link\": \"#read-more\", \"hero.static.headline\": \"Lisa Theme\", \"lisa.header.logo.text\": \"Event Agency\", \"hero.static.background\": \"\", \"hero.static.button.show\": \"0\", \"hero.static.description\": \"Free, clean and customizable theme for Tulia CMS\", \"lisa.layout.breadcrumbs\": \"show\", \"hero.static.button.label\": \"Read more\", \"lisa.footer.contact.type\": \"0\", \"lisa.footer.contact.email\": \"contact@event-agency.com\", \"lisa.footer.contact.phone\": \"+48 768 564 175\", \"lisa.footer.contact.address\": \"Event Agency,\\r\\nSt. Peter 1656,\\r\\nAlbuquerque\\r\\n\", \"lisa.footer.socials.twitter\": \"#twitter\", \"lisa.footer.socials.youtube\": \"#youtube\", \"lisa.footer.socials.facebook\": \"#facebook\", \"hero.static.background_mobile\": \"\", \"lisa.footer.contact.copyrights\": \"Tulia CMS\"}');
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__customizer_changeset_lang` (
  `customizer_changeset_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `autogenerated_locale` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `payload_localized` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `#__customizer_changeset_lang` (`customizer_changeset_id`, `locale`, `autogenerated_locale`, `payload_localized`) VALUES
('2c0bc11b-9733-4877-a50d-4413f4de33e6', 'en_US', 0, '[]'),
('2c0bc11b-9733-4877-a50d-4413f4de33e6', 'pl_PL', 1, '[]'),
('e890167e-fb55-4508-8486-3eaf54a164b9', 'en_US', 0, '[]'),
('e890167e-fb55-4508-8486-3eaf54a164b9', 'pl_PL', 0, '[]');
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__filemanager_directory` (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '00000000-0000-0000-0000-000000000000',
  `level` tinyint NOT NULL DEFAULT '0',
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__filemanager_file` (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `directory` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '00000000-0000-0000-0000-000000000000',
  `filename` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `extension` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'file',
  `mimetype` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` int DEFAULT NULL,
  `path` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `#__filemanager_file` (`id`, `directory`, `filename`, `extension`, `type`, `mimetype`, `size`, `path`, `created_at`, `updated_at`) VALUES
('5e2f9909-c497-4b05-ac62-b632f000a736', '00000000-0000-0000-0000-000000000000', 'denys-nevozhai-z0nvqfroqwa-unsplash.jpg', 'jpg', 'image', '', 2052321, 'uploads/2021/06', '2021-06-19 17:23:25', NULL),
('8d3d54b7-0c74-4423-bb1c-3014099e613e', '00000000-0000-0000-0000-000000000000', 'denys-nevozhai-z0nvqfroqwa-unsplash.jpg', 'jpg', 'image', '', 2052321, 'uploads/2022/02', '2022-02-17 17:02:37', NULL),
('ae642eef-754e-408f-9f0f-dccafce0afbe', '00000000-0000-0000-0000-000000000000', '91374-1.png', 'png', 'image', '', 259664, 'uploads/2021/06', '2021-06-18 06:05:29', NULL),
('db387e74-eeab-4d6c-b711-8c57ec02646a', '00000000-0000-0000-0000-000000000000', '91374.png', 'png', 'image', '', 259664, 'uploads/2021/06', '2021-06-18 06:04:48', NULL),
('e57f834a-17d6-4a42-8331-22a5cc508c8e', '00000000-0000-0000-0000-000000000000', 'alexandru-zdrobau-4bmtmxguvqo-unsplash.jpg', 'jpg', 'image', '', 1551268, 'uploads/2021/06', '2021-06-18 06:09:11', NULL),
('f59bb5de-23e4-4994-99e8-b21d8ca53349', '00000000-0000-0000-0000-000000000000', 'artem-kovalev-fk3xucftavk-unsplash.jpg', 'jpg', 'image', '', 664395, 'uploads/2021/06', '2021-06-18 06:24:23', NULL);
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__filemanager_image_thumbnail` (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `file_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `size` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `filename` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
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
CREATE TABLE `#__node_term_relationship` (
  `node_id` char(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `term_id` char(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `taxonomy` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `#__node_term_relationship` (`node_id`, `term_id`, `type`, `taxonomy`) VALUES
('04f47ff0-c066-4712-8e28-df676695924c', '2e2f328e-72ff-48d2-b423-4936890f7d33', 'MAIN', 'category'),
('11157ad7-a212-4c75-a989-c1234c99e0e3', '2e2f328e-72ff-48d2-b423-4936890f7d33', 'MAIN', 'category'),
('1de8280a-2363-4c09-bb4f-94e6b0ea2ef0', '58ed4a0c-4965-4a54-81d7-bcc51e52463e', 'MAIN', 'tag'),
('6e5445fa-4a40-46fa-89f8-72c572af9bb0', '2e2f328e-72ff-48d2-b423-4936890f7d33', 'MAIN', 'category');
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__option` (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `multilingual` tinyint(1) NOT NULL DEFAULT '0',
  `autoload` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__option_lang` (
  `option_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `#__option_lang` (`option_id`, `locale`, `value`) VALUES
('052f1830-0e93-4dc3-a787-e5e5c621688e', 'pl_PL', 'Website name'),
('aafa0c3f-0ab9-4d6d-9ba9-d7750d700748', 'pl_PL', 'Page is under maintenance mode. Please come back later.dfghdfgh');
EOF);
        $this->addSql(<<<EOF
CREATE TABLE `#__parameter` (
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `type` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `#__parameter` (`name`, `value`, `type`) VALUES
('modules.enabled.list', '[]', 'array');
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

INSERT INTO `#__term_attribute` (`id`, `owner_id`, `is_renderable`, `has_nonscalar_value`, `name`, `uri`, `value`, `compiled_value`, `payload`) VALUES
('00048ec0-b4f4-4dd7-9ef3-38e36ef0b524', '66435c83-584b-48ee-8115-db7aa23608b7', 0, 0, 'thumbnail', '', NULL, NULL, NULL),
('30cb5001-1488-4df0-bbc9-836cb1835183', 'a94d7e88-001a-45c1-99eb-c9dff24620b2', 0, 0, 'visibility', '', '1', NULL, NULL),
('3daa5ce4-dc1a-4454-9079-2ea6e4137736', '21d4cd46-64ce-45c6-ad2b-ad6281250726', 0, 0, 'thumbnail', '', NULL, NULL, NULL),
('619588a8-a543-4482-8cee-01dc1a44a393', '66435c83-584b-48ee-8115-db7aa23608b7', 0, 0, 'visibility', '', '1', NULL, NULL),
('b53cc7a8-19af-4e9c-9351-0200323aad49', '217ae341-de24-4b61-8405-66ec416b4398', 0, 0, 'thumbnail', '', NULL, NULL, NULL),
('ba439d5a-1298-4a5c-9c99-4fb1bcf8ca99', '58ed4a0c-4965-4a54-81d7-bcc51e52463e', 0, 0, 'thumbnail', '', NULL, NULL, NULL),
('d4e87841-0a42-4c4d-95b8-e00c652b4aef', '9a4c4ca3-4c83-46c0-8471-f2d5f8b9ef2c', 0, 0, 'thumbnail', '', NULL, NULL, NULL),
('e7f3743b-8b0f-427c-b2f0-6f28982d44cb', 'a94d7e88-001a-45c1-99eb-c9dff24620b2', 0, 0, 'thumbnail', '', NULL, NULL, NULL);
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
        /*$this->addSql(<<<EOF
CREATE TABLE `#__website` (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `backend_prefix` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '/administrator',
  `active` tinyint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `#__website` (`id`, `name`, `backend_prefix`, `active`) VALUES
('dd8a1b64-fefb-4089-bea4-13260a4d127e', 'Inactive website', '/administrator', 0),
('f19b16b2-f52b-442a-aee2-8e0f4fed31b7', 'Default website', '/administrator', 1),
('f5a4d537-17c5-4663-b521-c73a11554d8f', 'Tulia CMS', '/administrator', 1);
EOF);*/
        /*$this->addSql(<<<EOF
CREATE TABLE `#__website_locale` (
  `website_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `domain` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `domain_development` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `path_prefix` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ssl_mode` enum('FORCE_SSL','FORCE_NON_SSL','ALLOWED_BOTH') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `locale_prefix` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `#__website_locale` (`website_id`, `code`, `domain`, `domain_development`, `path_prefix`, `ssl_mode`, `locale_prefix`, `is_default`) VALUES
('dd8a1b64-fefb-4089-bea4-13260a4d127e', 'pl_PL', 'tulia.loc', 'tulia.loc', '/inactive', 'ALLOWED_BOTH', NULL, 1),
('f19b16b2-f52b-442a-aee2-8e0f4fed31b7', 'en_US', 'tulia.loc', 'tulia.loc', NULL, 'ALLOWED_BOTH', NULL, 1),
('f19b16b2-f52b-442a-aee2-8e0f4fed31b7', 'pl_PL', 'tulia.loc', 'tulia.loc', NULL, 'ALLOWED_BOTH', '/pl', 0),
('f5a4d537-17c5-4663-b521-c73a11554d8f', 'pl_PL', 'tulia.loc', 'tulia.loc', '/tulia', 'ALLOWED_BOTH', NULL, 1);
EOF);*/
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

ALTER TABLE `#__filemanager_directory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent` (`parent_id`);

ALTER TABLE `#__filemanager_file`
  ADD PRIMARY KEY (`id`),
  ADD KEY `directory` (`directory`);

ALTER TABLE `#__filemanager_image_thumbnail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_id` (`file_id`,`size`);

ALTER TABLE `#__model_change_history`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#__node_term_relationship`
  ADD PRIMARY KEY (`node_id`,`term_id`),
  ADD KEY `node_id` (`node_id`) USING BTREE,
  ADD KEY `term_id` (`term_id`) USING BTREE;

ALTER TABLE `#__option`
  ADD PRIMARY KEY (`name`),
  ADD UNIQUE KEY `UNIQUE` (`id`);

ALTER TABLE `#__option_lang`
  ADD UNIQUE KEY `option_id` (`option_id`);

ALTER TABLE `#__parameter`
  ADD PRIMARY KEY (`name`);

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

-- ALTER TABLE `#__website`
--   ADD PRIMARY KEY (`id`);
-- 
-- ALTER TABLE `#__website_locale`
--   ADD UNIQUE KEY `UNIQUE` (`website_id`,`code`,`domain`) USING BTREE,
--   ADD KEY `website_id` (`website_id`);

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

ALTER TABLE `#__option_lang`
  ADD CONSTRAINT `fk_option_id` FOREIGN KEY (`option_id`) REFERENCES `#__option` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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

--- ALTER TABLE `#__website_locale`
---   ADD CONSTRAINT `fk_website_has_locale_website_id` FOREIGN KEY (`website_id`) REFERENCES `#__website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
---
EOF
);
    }

    public function down(Schema $schema) : void
    {
    }
}
