<?php

require_once __DIR__ . '/vendor/autoload.php';

$finder = PhpCsFixer\Finder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__)
    ->exclude([
        'vendor',
    ])
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,

        // exceptions
        'single_line_throw' => false,

        // php file
        'concat_space' => ['spacing' => 'one'],

        // namespace and imports
        'ordered_imports' => true,
        'global_namespace_import' => [
            'import_classes' => false,
            'import_constants' => false,
            'import_functions' => false,
        ],

        // standard functions and operators
        'native_constant_invocation' => false,
        'native_function_invocation' => false,
        'modernize_types_casting' => true,
        'is_null' => true,

        // arrays
        'array_syntax' => [
            'syntax' => 'short',
        ],

        // phpdoc
        'phpdoc_annotation_without_dot' => false,
        'phpdoc_summary' => false,

        // logical operators
        'logical_operators' => true,
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '.php_cs.cache')
;
