<?php
namespace Deployer;
require 'recipe/laravel.php';

// Configuration

set('ssh_type', 'native');
set('ssh_multiplexing', true);

set('repository', 'git@github.com:yxwzaxns/lblog.git');

add('shared_files', [
    '.env',
]);
add('shared_dirs', []);

add('writable_dirs', [
    'bootstrap/cache',
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
]);

// Servers

server('production', '120.24.65.9')
    ->user('web')
    ->identityFile()
    ->set('deploy_path', '/home/web/www');
//    ->pty(true);


// Tasks
desc('Execute artisan migrate:install');
task('artisan:migrate:install', function () {
    $output = run('{{bin/php}} {{release_path}}/artisan migrate:install');
    writeln('<info>' . $output . '</info>');
});

desc('Restart PHP-FPM service');
task('php-fpm:restart', function () {
    // The user must have rights for restart service
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart php-fpm.service
    run('sudo service php7.0-fpm restart');
});

desc('Deploy your project');
task('deploy', [
    'artisan:migrate:install',
    'artisan:migrate',
]);

after('deploy:symlink', 'php-fpm:restart');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

//before('deploy:symlink', );

after('deploy', 'success');