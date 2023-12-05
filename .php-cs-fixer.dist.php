<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->in('src/lib')
    ->exclude('src/client')
    ->exclude('src/vendor')
    ->exclude('src/cache')
    ->exclude('src/log')
    ->exclude('src/config/proxy')
    ->exclude('devTools/core/vendor')
    ->exclude('src/test/functional/node_modules')
    ->exclude('installer/client')
    // TODO:: Remove bellow excluded dirs, files
    ->exclude('devTools/load')
    ->exclude('lib')
    ->exclude('src/plugins/orangehrmRESTPlugin');

$config = new PhpCsFixer\Config();
return $config->setRules(
    [
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'doctrine_annotation_indentation' => true,
        'doctrine_annotation_spaces' => [
            'after_array_assignments_equals' => false,
            'before_array_assignments_equals' => false
        ],
        'simple_to_complex_string_variable' => true,
    ]
)->setFinder($finder);
