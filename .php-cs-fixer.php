<?php
// https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/master/doc/config.rst

$finder = (new PhpCsFixer\Finder())->in(__DIR__);

$config = new PhpCsFixer\Config();
$config->setRiskyAllowed(true);
$config->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect()); // Enable multicore.

$rules  = [
//    '@PHP81Migration'       => true, // Must be the same as the min PHP version.
//    '@PHP80Migration:risky' => true,
'modernize_strpos'                       => true,
'php_unit_set_up_tear_down_visibility'   => true,

];

return $config->setRules($rules)->setFinder($finder);
