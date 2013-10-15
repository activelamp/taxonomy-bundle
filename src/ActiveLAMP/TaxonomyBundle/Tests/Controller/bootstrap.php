<?php
/**
 * @file
 * Bootstrap file for functional testing.
 */

// Set the KERNEL_DIR environment variable
$_SERVER['KERNEL_DIR'] = realpath(__DIR__ . '/../../../../../../../../app');

if (!@include __DIR__ . '/../../../../../../../../vendor/autoload.php') {
    die(<<<'EOT'
You must set up the project dependencies, run the following commands:
wget http://getcomposer.org/composer.phar
php composer.phar install
EOT
    );
}