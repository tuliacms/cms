<?php declare(strict_types=1);
namespace Deployer;

require './vendor/tuliacms/cms/src/Cms/Deployment/Infrastructure/Framework/Resources/recipes/deploy.tuliacms.smallpl.php';

// Config
set('repository', '[[repository]]');
set('deploy_path', '/usr/home/[[hosting.user]]/domains/[[domain]]');
set('cachetool_args', '--web --web-path={{deploy_path}}/public_html --web-url=https://[[domain]]');
set('tulia.server.smallpl.deploy_domain', '[[domain]]');

// Hosts
host('[[hosting.host]]')
    ->set('remote_user', '[[hosting.user]]')
    ->set('http_user', '[[hosting.user]]')
    ->set('server_username', '[[hosting.user]]');
