<?php declare(strict_types=1);
namespace Deployer;

// Files autoloaded by Deployer
require 'recipe/symfony.php';
require 'contrib/cachetool.php';
require './vendor/tuliacms/cms/src/Cms/Deployment/Infrastructure/Framework/Resources/recipes/deploy.tuliacms.php';

set('bin/php', '/usr/local/bin/php81');
set('bin/composer', 'composer2');

task('tulia:server:configure:small.pl', function () {
    run('devil binexec on');
    run('devil www options {{tulia.server.smallpl.deploy_domain}} php_openbasedir {{deploy_path}}/');

    // Change Composer CLI's PHP version
    run('rm ~/bin/php && ln -s {{bin/php}} ~/bin/php');
    run('echo \'export PATH=$HOME/bin:$PATH\' >> $HOME/.bash_profile');
    run('source $HOME/.bash_profile');

    // Force PHP81 for this domain
    if (!test("[ -f {{deploy_path}}/.htaccess ]")) {
        $htaccess = <<<EOF
AddType application/x-httpd-php81 .php
Options -Indexes
EOF;

        run("echo '{$htaccess}' >> {{deploy_path}}/.htaccess");
    }

    // Set production user.s php.ini settings
    if (!test("[ -f {{deploy_path}}/.user.ini ]")) {
        $phpini = <<<EOF
error_reporting = 0
display_errors = Off
log_errors = On
error_log = "/usr/home/tuliacms/domains/{{tulia.server.smallpl.deploy_domain}}/logs/phperror.log"
include_path = ".:/usr/home/tuliacms/domains/{{tulia.server.smallpl.deploy_domain}}:/usr/local/share/pear"
EOF;

        run("echo '{$phpini}' >> {{deploy_path}}/.user.ini");
    }
});

after('deploy:setup', 'tulia:server:configure:small.pl');
after('deploy:symlink', 'cachetool:clear:opcache');
