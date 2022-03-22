<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->in('symfony/lib')
    ->exclude('symfony/client')
    ->exclude('symfony/vendor')
    ->exclude('symfony/cache')
    ->exclude('symfony/log')
    ->exclude('symfony/config/proxy')
    ->exclude('devTools/core/vendor')
    ->exclude('symfony/test/functional/node_modules')
    // TODO:: Remove bellow excluded dirs, files
    ->notPath('index.php')
    ->notPath('install.php')
    ->exclude('devTools/general')
    ->exclude('devTools/load')
    ->exclude('installer')
    ->exclude('lib')
    ->exclude('symfony/plugins/orangehrmAdminPlugin/lib')
    ->exclude('symfony/plugins/orangehrmAdminPlugin/test/model')
    ->exclude('symfony/plugins/orangehrmAttendancePlugin/test/model')
    ->exclude('symfony/plugins/orangehrmBuzzPlugin')
    ->exclude('symfony/plugins/orangehrmCoreWebServicePlugin')
    ->exclude('symfony/plugins/orangehrmCorePlugin/test/authorization')
    ->exclude('symfony/plugins/orangehrmCorePlugin/test/cache')
    ->exclude('symfony/plugins/orangehrmCorePlugin/test/components')
    ->exclude('symfony/plugins/orangehrmCorePlugin/test/dao')
    ->exclude('symfony/plugins/orangehrmCorePlugin/test/factory')
    ->exclude('symfony/plugins/orangehrmCorePlugin/test/form')
    ->exclude('symfony/plugins/orangehrmCorePlugin/test/model')
    ->exclude('symfony/plugins/orangehrmCorePlugin/test/utility')
    ->exclude('symfony/plugins/orangehrmCorporateBrandingPlugin')
    ->exclude('symfony/plugins/orangehrmCorporateDirectoryPlugin')
    ->exclude('symfony/plugins/orangehrmDashboardPlugin')
    ->exclude('symfony/plugins/orangehrmHelpPlugin/test')
    ->exclude('symfony/plugins/orangehrmMaintenancePlugin/test/model')
    ->exclude('symfony/plugins/orangehrmMarketPlacePlugin')
    ->exclude('symfony/plugins/orangehrmOpenidAuthenticationPlugin')
    ->exclude('symfony/plugins/orangehrmPerformancePlugin')
    ->exclude('symfony/plugins/orangehrmPerformancePlugin/test/model')
    ->exclude('symfony/plugins/orangehrmPerformanceTrackerPlugin')
    ->exclude('symfony/plugins/orangehrmPimPlugin/test/model')
    ->exclude('symfony/plugins/orangehrmRecruitmentPlugin')
    ->exclude('symfony/plugins/orangehrmRESTPlugin');

$config = new PhpCsFixer\Config();
return $config->setRules(
    [
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
    ]
)->setFinder($finder);
