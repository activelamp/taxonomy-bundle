<?php
$app_dir = __DIR__ . '/../../../symfony/app';

function add_bundle($bundle_class, $app_dir) {
    $contents = file_get_contents($app_dir . '/AppKernel.php');

    $length = strlen($contents);
    $return_pos = strpos($contents, 'return $bundles');

    $h = fopen($app_dir . '/AppKernel.php', 'c+');
    fseek($h, $return_pos);
    fwrite($h, '$bundles[] = new ' . $bundle_class .";\n\t");
    fwrite($h, substr($contents, $return_pos - $length));
    fclose($h);
}

function add_route($app_dir) {
    $contents = file_get_contents(__DIR__ . '/routing.yml');
    $h = fopen($app_dir . '/config/routing.yml', 'w');
    fwrite($h, $contents ."\n");
    fclose($h);
}

add_bundle('ActiveLAMP\TaxonomyBundle\ALTaxonomyBundle()', $app_dir);
add_route($app_dir);