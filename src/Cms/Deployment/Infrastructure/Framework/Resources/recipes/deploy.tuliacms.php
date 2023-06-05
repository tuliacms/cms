<?php declare(strict_types=1);
namespace Deployer;

require_once './vendor/symfony/dotenv/Dotenv.php';
require_once './vendor/symfony/dotenv/Exception/ExceptionInterface.php';
require_once './vendor/symfony/dotenv/Exception/FormatException.php';
require_once './vendor/symfony/dotenv/Exception/FormatExceptionContext.php';
require_once './vendor/symfony/dotenv/Exception/PathException.php';

(new \Symfony\Component\Dotenv\Dotenv())->load('.env', '.env.prod');

/** @var array $url */
$url = parse_url($_ENV['DATABASE_URL']);

set('current_path', '{{deploy_path}}/current');
set('keep_releases', 4);
add('shared_dirs', [
    'var/log',
    'public/assets',
    'public/uploads',
]);
set('tulia.dbdump.database', ltrim($url['path'], '/'));
set('tulia.dbdump.user', $url['user']);
set('tulia.dbdump.host', $url['host']);
set('tulia.dbdump.password', $url['pass']);

task('tulia:publish-assets', function () {
    run('cd {{release_or_current_path}} && {{bin/console}} assets:publish -q');
});

task('tulia:dump_env_local', function () {
    $buildVersion = date('ymdhi');
    run(sprintf('cd {{release_path}} && cp .env.prod .env && echo "BUILD_VERSION=%s" >> .env', $buildVersion));
});

task('deploy:make:index:executable', function () {
    run('chmod 777 {{deploy_path}}/public_html/index.php');
});

task('tulia:disable_htaccess', function () {
    if (test('[ -f {{deploy_path}}/public_html/.htaccess ]')) {
        run('mv {{deploy_path}}/public_html/.htaccess {{deploy_path}}/public_html/.htaccess.turned-off');
    }
});

task('tulia:enable_htaccess', function () {
    if (test('[ -f {{deploy_path}}/public_html/.htaccess.turned-off ]')) {
        run('mv {{deploy_path}}/public_html/.htaccess.turned-off {{deploy_path}}/public_html/.htaccess');
    }
});

task('tulia:symlink:current2public', function () {
    run('ln -sf {{current_path}}/public {{deploy_path}}/public_html');
});

$synchronizeDatabase = function () {
    $host = currentHost()->getHostname();
    $remoteUser = currentHost()->get('remote_user');

    info('Dumping local database...');
    runLocally('make dump-database');
    info('Sending database dump to remote server...');
    runLocally("rsync -av --rsh=ssh var/mysqldump.sql {$remoteUser}@{$host}:{{deploy_path}}");
    info('Importing database...');
    run('chmod 764 {{deploy_path}}/mysqldump.sql');
    run('cd {{deploy_path}} && mysql --database={{tulia.dbdump.database}} --user={{tulia.dbdump.user}} --host={{tulia.dbdump.host}} --password={{tulia.dbdump.password}} < mysqldump.sql');
    run('rm {{deploy_path}}/mysqldump.sql');
    runLocally('rm var/mysqldump.sql');
};

task('tulia:database:synchronize', $synchronizeDatabase);
task('tulia:database:synchronize.ask', function () use ($synchronizeDatabase) {
    // If this is a first release.
    if (test('[ -d {{current_path}} ]')) {
        return;
    }

    if (!askConfirmation('First deploy detected, do You want to synchronize local database to remote?')) {
        return;
    }

    $synchronizeDatabase();
});

$synchronizePublic = function () {
    $host = currentHost()->getHostname();
    $remoteUser = currentHost()->get('remote_user');

    info('Creating public.uploads.zip...');
    runLocally('cd public/uploads && zip -r ./../../var/public.uploads.zip .');
    info('Sending public.uploads.zip to remote server (this may take some time...)');
    runLocally("rsync --progress -av --rsh=ssh var/public.uploads.zip {$remoteUser}@{$host}:{{release_or_current_path}}");
    run('rm -rf {{release_or_current_path}}/public/uploads/{*}');
    run('unzip {{release_or_current_path}}/public.uploads.zip -d {{deploy_path}}/shared/public/uploads');
    run('rm {{release_or_current_path}}/public.uploads.zip');
    runLocally('rm var/public.uploads.zip');
};

task('tulia:public:synchronize', $synchronizePublic);
task('tulia:public:synchronize.ask', function () use ($synchronizePublic) {
    // If this is a first release.
    if (test('[ -d {{current_path}} ]')) {
        return;
    }

    if (!askConfirmation('First deploy detected, do You want to synchronize local /public directory to remote?')) {
        return;
    }

    $synchronizePublic();
});

after('deploy:failed', 'deploy:unlock');

before('cachetool:clear:opcache', 'tulia:disable_htaccess');
after('cachetool:clear:opcache', 'tulia:enable_htaccess');

after('deploy:symlink', 'tulia:symlink:current2public');
after('tulia:symlink:current2public', 'deploy:make:index:executable');
before('deploy:vendors', 'tulia:dump_env_local');
after('deploy:vendors', 'database:migrate');
after('tulia:enable_htaccess', 'tulia:database:synchronize.ask');
after('tulia:database:synchronize.ask', 'tulia:public:synchronize.ask');
after('tulia:public:synchronize.ask', 'tulia:publish-assets');
