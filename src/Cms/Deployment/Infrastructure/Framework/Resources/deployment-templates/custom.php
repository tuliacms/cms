<?php
namespace Deployer;

require 'recipe/symfony.php';

// Config
set('repository', '[[repository]]');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts
host('[[hosting.host]]')
    ->set('remote_user', '[[hosting.user]]')
    ->set('deploy_path', '~/project');

// Hooks
after('deploy:failed', 'deploy:unlock');
