<?php
// https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/master/doc/config.rst

$rules  = [
//    '@autoPHPMigration'       => true, // Must be the same as the min PHP version.
//    '@autoPHPMigration:risky' => true,
//'@PHPUnit11x0Migration:risky' => true,
'modernize_strpos'                       => true,
/*'php_unit_attributes'                    => true,
'php_unit_construct'                     => true,
'php_unit_method_casing'                 => true,
'php_unit_assert_new_names'              => true,
'php_unit_dedicate_assert'               => true,
'php_unit_dedicate_assert_internal_type' => true,
'php_unit_set_up_tear_down_visibility'   => true,*/
];

$finder = new PhpCsFixer\Finder();
$finder->in(__DIR__);
$finder->exclude(['migrations']);

$config = new PhpCsFixer\Config();
$config->setRiskyAllowed(true);
$config->setUsingCache(false);
$config->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect()); // Enable multicore.
$config->setRules($rules);
$config->setFinder($finder);

return $config;
