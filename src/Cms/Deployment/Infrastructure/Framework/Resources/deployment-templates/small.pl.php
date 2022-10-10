<?php
namespace Deployer;

require 'recipe/symfony.php';
require 'contrib/cachetool.php';

// Config
set('repository', '[[repository]]');
set('http_user', '[[hosting.user]]');
set('server_username', '[[hosting.user]]');
set('bin/php', '/usr/local/bin/php81');
set('bin/composer', 'composer2');
set('deploy_path', '/usr/home/[[hosting.user]]/domains/[[domain]]');
set('current_path', '{{deploy_path}}/current');
set('cachetool_args', '--web --web-path={{deploy_path}}/public_html --web-url=https://[[domain]]');
set('keep_releases', 4);
add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts
host('[[hosting.host]]')
    ->set('remote_user', '[[hosting.user]]');

// Hooks
task('server:configure:small.pl', function () {
    run('devil binexec on');
    run('devil www options [[domain]] php_openbasedir ~/domains/[[domain]]/');

    // Change Composer CLI's PHP version
    run('rm ~/bin/php && ln -s {{bin/php}} ~/bin/php');
    run('echo \'export PATH=$HOME/bin:$PATH\' >> $HOME/.bash_profile');
    run('source $HOME/.bash_profile');
});

task('dump_env_local', function () {
    $buildVersion = date('ymdhi');
    run(sprintf('cd {{release_path}} && cp .env.prod .env && echo "BUILD_VERSION=%s" >> .env', $buildVersion));
});

task('disable_htaccess', function () {
    run('mv {{deploy_path}}/public_html/.htaccess {{deploy_path}}/public_html/.htaccess.turned-off');
});

task('enable_htaccess', function () {
    run('mv {{deploy_path}}/public_html/.htaccess.turned-off {{deploy_path}}/public_html/.htaccess');
});

task('deploy:symlink:current2public', function () {
    run('ln -sf {{current_path}}/public {{deploy_path}}/public_html');
});

task('deploy:make:index:executable', function () {
    run('chmod 777 {{deploy_path}}/public_html/index.php');
});

after('deploy:failed', 'deploy:unlock');

before('cachetool:clear:opcache', 'disable_htaccess');
after('cachetool:clear:opcache', 'enable_htaccess');

after('deploy:setup', 'server:configure:small.pl');
after('deploy:symlink', 'deploy:symlink:current2public');
after('deploy:symlink:current2public', 'deploy:make:index:executable');
after('deploy:symlink', 'cachetool:clear:opcache');
before('deploy:vendors', 'dump_env_local');
after('deploy:vendors', 'database:migrate');
