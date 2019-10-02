<?php
namespace Deployer;
require 'recipe/symfony.php';

// Configuration

set('ssh_type', 'native');
set('ssh_multiplexing', true);

set('git_tty', true);

set('repository', 'git@bitbucket.org:phoenix-soft/edward-webapp.git');

set('bin_dir', 'bin');
set('var_dir', 'var');

set('shared_dirs', ['var/logs', 'var/sessions', 'web/uploads', 'web/media']);
add('shared_files', []);
set('writable_dirs', ['var/cache', 'var/logs', 'var/sessions', 'web/uploads', 'web/media']);

// Servers

server('stage', '94.130.178.198')
    ->user('xoptov')
    ->identityFile('~/.ssh/id_rsa')
    ->set('deploy_path', '/var/www/stage.edward-lead.ru')
    ->pty(true);


server('prod', '94.130.178.198')
    ->user('xoptov')
    ->identityFile('~/.ssh/id_rsa')
    ->set('deploy_path', '/var/www/cabinet.edward-lead.ru')
    ->pty(true);

// Tasks

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'database:migrate');
