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
    ->exclude('src/plugins/orangehrmBuzzPlugin')
    ->exclude('src/plugins/orangehrmHelpPlugin/test')
    ->exclude('src/plugins/orangehrmMarketPlacePlugin')
    ->exclude('src/plugins/orangehrmOpenidAuthenticationPlugin')
    ->exclude('src/plugins/orangehrmRESTPlugin');

$config = new PhpCsFixer\Config();
return $config->setRules(
    [
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
    ]
)->setFinder($finder);
