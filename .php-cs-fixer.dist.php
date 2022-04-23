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
    ->exclude('devTools/general')
    ->exclude('devTools/load')
    ->exclude('lib')
    ->exclude('src/plugins/orangehrmAdminPlugin/lib')
    ->exclude('src/plugins/orangehrmAdminPlugin/test/model')
    ->exclude('src/plugins/orangehrmAttendancePlugin/test/model')
    ->exclude('src/plugins/orangehrmBuzzPlugin')
    ->exclude('src/plugins/orangehrmCoreWebServicePlugin')
    ->exclude('src/plugins/orangehrmCorePlugin/test/authorization')
    ->exclude('src/plugins/orangehrmCorePlugin/test/cache')
    ->exclude('src/plugins/orangehrmCorePlugin/test/components')
    ->exclude('src/plugins/orangehrmCorePlugin/test/dao')
    ->exclude('src/plugins/orangehrmCorePlugin/test/factory')
    ->exclude('src/plugins/orangehrmCorePlugin/test/form')
    ->exclude('src/plugins/orangehrmCorePlugin/test/model')
    ->exclude('src/plugins/orangehrmCorePlugin/test/utility')
    ->exclude('src/plugins/orangehrmCorporateBrandingPlugin')
    ->exclude('src/plugins/orangehrmCorporateDirectoryPlugin')
    ->exclude('src/plugins/orangehrmDashboardPlugin')
    ->exclude('src/plugins/orangehrmHelpPlugin/test')
    ->exclude('src/plugins/orangehrmMaintenancePlugin/test/model')
    ->exclude('src/plugins/orangehrmMarketPlacePlugin')
    ->exclude('src/plugins/orangehrmOpenidAuthenticationPlugin')
    ->exclude('src/plugins/orangehrmPerformancePlugin')
    ->exclude('src/plugins/orangehrmPerformancePlugin/test/model')
    ->exclude('src/plugins/orangehrmPerformanceTrackerPlugin')
    ->exclude('src/plugins/orangehrmPimPlugin/test/model')
    ->exclude('src/plugins/orangehrmRecruitmentPlugin')
    ->exclude('src/plugins/orangehrmRESTPlugin');

$config = new PhpCsFixer\Config();
return $config->setRules(
    [
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
    ]
)->setFinder($finder);
