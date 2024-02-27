<?php
namespace Deployer;

require 'recipe/symfony.php';

// Config
set('application', 'symfony');
set('repository', 'git@github.com:mbvb1223/cct.git');
set('sub_directory', 'code');
add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('52.221.129.246')
    ->set('remote_user', 'ubuntu')
    ->set('branch', 'CCT-6_Deployer')
    ->set('identity_file', '../../../khienlien1223.pem')
    ->set('deploy_path', '~/{{application}}');

// Hooks
after('deploy:failed', 'deploy:unlock');
