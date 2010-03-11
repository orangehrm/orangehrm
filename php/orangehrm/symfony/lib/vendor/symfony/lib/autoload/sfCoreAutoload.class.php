<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * The current symfony version.
 */
define('SYMFONY_VERSION', '1.2.8');

/**
 * sfCoreAutoload class.
 *
 * This class is a singleton as PHP seems to be unable to register 2 autoloaders that are instances
 * of the same class (why?).
 *
 * @package    symfony
 * @subpackage autoload
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfCoreAutoload.class.php 20162 2009-07-13 18:11:02Z FabianLange $
 */
class sfCoreAutoload
{
  static protected
    $registered = false,
    $instance   = null;

  protected
    $baseDir = '';

  protected function __construct()
  {
    $this->baseDir = realpath(dirname(__FILE__).'/..').'/';
  }

  /**
   * Retrieves the singleton instance of this class.
   *
   * @return sfCoreAutoload A sfCoreAutoload implementation instance.
   */
  static public function getInstance()
  {
    if (!isset(self::$instance))
    {
      self::$instance = new sfCoreAutoload();
    }

    return self::$instance;
  }

  /**
   * Register sfCoreAutoload in spl autoloader.
   *
   * @return void
   */
  static public function register()
  {
    if (self::$registered)
    {
      return;
    }

    ini_set('unserialize_callback_func', 'spl_autoload_call');
    if (false === spl_autoload_register(array(self::getInstance(), 'autoload')))
    {
      throw new sfException(sprintf('Unable to register %s::autoload as an autoloading method.', get_class(self::getInstance())));
    }

    self::$registered = true;
  }

  /**
   * Unregister sfCoreAutoload from spl autoloader.
   *
   * @return void
   */
  static public function unregister()
  {
    spl_autoload_unregister(array(self::getInstance(), 'autoload'));
    self::$registered = false;
  }

  /**
   * Handles autoloading of classes.
   *
   * @param string $class A class name.
   *
   * @return boolean Returns true if the class has been loaded
   */
  public function autoload($class)
  {
    if (!isset($this->classes[$class]))
    {
      return false;
    }

    require $this->baseDir.$this->classes[$class].'/'.$class.'.class.php';

    return true;
  }

  /**
   * Returns the base directory this autoloader is working on.
   *
   * @return base directory
   */
  public function getBaseDir()
  {
    return $this->baseDir;
  }

  /**
   * Rebuilds the association array between class names and paths.
   *
   * This method overrides this file (__FILE__)
   */
  static public function make()
  {
    $libDir = str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'));
    require_once $libDir.'/util/sfFinder.class.php';

    $files = sfFinder::type('file')
      ->prune('plugins')
      ->prune('vendor')
      ->prune('skeleton')
      ->prune('default')
      ->name('*\.class\.php')
      ->in($libDir)
    ;

    sort($files, SORT_STRING);

    $classes = array();
    foreach ($files as $file)
    {
      $classes[basename($file, '.class.php')] = str_replace($libDir.'/', '', str_replace(DIRECTORY_SEPARATOR, '/', dirname($file)));
    }

    $content = preg_replace('/protected \$classes = array *\(.*?\)/s', 'protected $classes = '.var_export($classes, true), file_get_contents(__FILE__));

    file_put_contents(__FILE__, $content);
  }

  // Don't edit this property by hand.
  // To update it, use sfCoreAutoload::make()
  protected $classes = array (
  'sfAction' => 'action',
  'sfActionStack' => 'action',
  'sfActionStackEntry' => 'action',
  'sfActions' => 'action',
  'sfComponent' => 'action',
  'sfComponents' => 'action',
  'sfData' => 'addon',
  'sfPager' => 'addon',
  'sfAutoload' => 'autoload',
  'sfCoreAutoload' => 'autoload',
  'sfSimpleAutoload' => 'autoload',
  'sfAPCCache' => 'cache',
  'sfCache' => 'cache',
  'sfEAcceleratorCache' => 'cache',
  'sfFileCache' => 'cache',
  'sfFunctionCache' => 'cache',
  'sfMemcacheCache' => 'cache',
  'sfNoCache' => 'cache',
  'sfSQLiteCache' => 'cache',
  'sfXCacheCache' => 'cache',
  'sfAnsiColorFormatter' => 'command',
  'sfCommandApplication' => 'command',
  'sfCommandArgument' => 'command',
  'sfCommandArgumentSet' => 'command',
  'sfCommandArgumentsException' => 'command',
  'sfCommandException' => 'command',
  'sfCommandLogger' => 'command',
  'sfCommandManager' => 'command',
  'sfCommandOption' => 'command',
  'sfCommandOptionSet' => 'command',
  'sfFormatter' => 'command',
  'sfSymfonyCommandApplication' => 'command',
  'sfApplicationConfiguration' => 'config',
  'sfAutoloadConfigHandler' => 'config',
  'sfCacheConfigHandler' => 'config',
  'sfCompileConfigHandler' => 'config',
  'sfConfig' => 'config',
  'sfConfigCache' => 'config',
  'sfConfigHandler' => 'config',
  'sfDatabaseConfigHandler' => 'config',
  'sfDefineEnvironmentConfigHandler' => 'config',
  'sfFactoryConfigHandler' => 'config',
  'sfFilterConfigHandler' => 'config',
  'sfGeneratorConfigHandler' => 'config',
  'sfLoader' => 'config',
  'sfPluginConfiguration' => 'config',
  'sfPluginConfigurationGeneric' => 'config',
  'sfProjectConfiguration' => 'config',
  'sfRootConfigHandler' => 'config',
  'sfRoutingConfigHandler' => 'config',
  'sfSecurityConfigHandler' => 'config',
  'sfSimpleYamlConfigHandler' => 'config',
  'sfViewConfigHandler' => 'config',
  'sfYamlConfigHandler' => 'config',
  'sfConsoleController' => 'controller',
  'sfController' => 'controller',
  'sfFrontWebController' => 'controller',
  'sfWebController' => 'controller',
  'sfDatabase' => 'database',
  'sfDatabaseManager' => 'database',
  'sfMySQLDatabase' => 'database',
  'sfMySQLiDatabase' => 'database',
  'sfPDODatabase' => 'database',
  'sfPostgreSQLDatabase' => 'database',
  'sfDebug' => 'debug',
  'sfTimer' => 'debug',
  'sfTimerManager' => 'debug',
  'sfWebDebug' => 'debug',
  'sfWebDebugPanel' => 'debug',
  'sfWebDebugPanelCache' => 'debug',
  'sfWebDebugPanelConfig' => 'debug',
  'sfWebDebugPanelLogs' => 'debug',
  'sfWebDebugPanelMemory' => 'debug',
  'sfWebDebugPanelSymfonyVersion' => 'debug',
  'sfWebDebugPanelTimer' => 'debug',
  'sfEvent' => 'event',
  'sfEventDispatcher' => 'event',
  'sfCacheException' => 'exception',
  'sfConfigurationException' => 'exception',
  'sfControllerException' => 'exception',
  'sfDatabaseException' => 'exception',
  'sfError404Exception' => 'exception',
  'sfException' => 'exception',
  'sfFactoryException' => 'exception',
  'sfFileException' => 'exception',
  'sfFilterException' => 'exception',
  'sfForwardException' => 'exception',
  'sfInitializationException' => 'exception',
  'sfParseException' => 'exception',
  'sfRenderException' => 'exception',
  'sfSecurityException' => 'exception',
  'sfStopException' => 'exception',
  'sfStorageException' => 'exception',
  'sfViewException' => 'exception',
  'sfBasicSecurityFilter' => 'filter',
  'sfCacheFilter' => 'filter',
  'sfCommonFilter' => 'filter',
  'sfExecutionFilter' => 'filter',
  'sfFilter' => 'filter',
  'sfFilterChain' => 'filter',
  'sfRenderingFilter' => 'filter',
  'sfForm' => 'form',
  'sfFormField' => 'form',
  'sfFormFieldSchema' => 'form',
  'sfFormFilter' => 'form',
  'sfAdminGenerator' => 'generator',
  'sfCrudGenerator' => 'generator',
  'sfGenerator' => 'generator',
  'sfGeneratorManager' => 'generator',
  'sfModelGenerator' => 'generator',
  'sfModelGeneratorConfiguration' => 'generator',
  'sfModelGeneratorConfigurationField' => 'generator',
  'sfModelGeneratorHelper' => 'generator',
  'sfRichTextEditor' => 'helper',
  'sfRichTextEditorFCK' => 'helper',
  'sfRichTextEditorTinyMCE' => 'helper',
  'TGettext' => 'i18n/Gettext',
  'sfI18nApplicationExtract' => 'i18n/extract',
  'sfI18nExtract' => 'i18n/extract',
  'sfI18nExtractorInterface' => 'i18n/extract',
  'sfI18nModuleExtract' => 'i18n/extract',
  'sfI18nPhpExtractor' => 'i18n/extract',
  'sfI18nYamlExtractor' => 'i18n/extract',
  'sfI18nYamlGeneratorExtractor' => 'i18n/extract',
  'sfI18nYamlValidateExtractor' => 'i18n/extract',
  'sfChoiceFormat' => 'i18n',
  'sfCultureInfo' => 'i18n',
  'sfDateFormat' => 'i18n',
  'sfDateTimeFormatInfo' => 'i18n',
  'sfI18N' => 'i18n',
  'sfIMessageSource' => 'i18n',
  'sfMessageFormat' => 'i18n',
  'sfMessageSource' => 'i18n',
  'sfMessageSource_Aggregate' => 'i18n',
  'sfMessageSource_Database' => 'i18n',
  'sfMessageSource_File' => 'i18n',
  'sfMessageSource_MySQL' => 'i18n',
  'sfMessageSource_SQLite' => 'i18n',
  'sfMessageSource_XLIFF' => 'i18n',
  'sfMessageSource_gettext' => 'i18n',
  'sfNumberFormat' => 'i18n',
  'sfNumberFormatInfo' => 'i18n',
  'sfAggregateLogger' => 'log',
  'sfConsoleLogger' => 'log',
  'sfFileLogger' => 'log',
  'sfLogger' => 'log',
  'sfLoggerInterface' => 'log',
  'sfLoggerWrapper' => 'log',
  'sfNoLogger' => 'log',
  'sfStreamLogger' => 'log',
  'sfVarLogger' => 'log',
  'sfWebDebugLogger' => 'log',
  'sfPearConfig' => 'plugin',
  'sfPearDownloader' => 'plugin',
  'sfPearEnvironment' => 'plugin',
  'sfPearFrontendPlugin' => 'plugin',
  'sfPearRest' => 'plugin',
  'sfPearRest10' => 'plugin',
  'sfPearRest11' => 'plugin',
  'sfPearRestPlugin' => 'plugin',
  'sfPluginDependencyException' => 'plugin',
  'sfPluginException' => 'plugin',
  'sfPluginManager' => 'plugin',
  'sfPluginRecursiveDependencyException' => 'plugin',
  'sfPluginRestException' => 'plugin',
  'sfSymfonyPluginManager' => 'plugin',
  'sfConsoleRequest' => 'request',
  'sfRequest' => 'request',
  'sfWebRequest' => 'request',
  'sfConsoleResponse' => 'response',
  'sfResponse' => 'response',
  'sfWebResponse' => 'response',
  'sfNoRouting' => 'routing',
  'sfObjectRoute' => 'routing',
  'sfObjectRouteCollection' => 'routing',
  'sfPathInfoRouting' => 'routing',
  'sfPatternRouting' => 'routing',
  'sfRequestRoute' => 'routing',
  'sfRoute' => 'routing',
  'sfRouteCollection' => 'routing',
  'sfRouting' => 'routing',
  'sfCacheSessionStorage' => 'storage',
  'sfDatabaseSessionStorage' => 'storage',
  'sfMySQLSessionStorage' => 'storage',
  'sfMySQLiSessionStorage' => 'storage',
  'sfNoStorage' => 'storage',
  'sfPDOSessionStorage' => 'storage',
  'sfPostgreSQLSessionStorage' => 'storage',
  'sfSessionStorage' => 'storage',
  'sfSessionTestStorage' => 'storage',
  'sfStorage' => 'storage',
  'sfAppRoutesTask' => 'task/app',
  'sfCacheClearTask' => 'task/cache',
  'sfConfigureAuthorTask' => 'task/configure',
  'sfConfigureDatabaseTask' => 'task/configure',
  'sfGenerateAppTask' => 'task/generator',
  'sfGenerateModuleTask' => 'task/generator',
  'sfGenerateProjectTask' => 'task/generator',
  'sfGenerateTaskTask' => 'task/generator',
  'sfGeneratorBaseTask' => 'task/generator',
  'sfHelpTask' => 'task/help',
  'sfListTask' => 'task/help',
  'sfI18nExtractTask' => 'task/i18n',
  'sfI18nFindTask' => 'task/i18n',
  'sfLogClearTask' => 'task/log',
  'sfLogRotateTask' => 'task/log',
  'sfPluginAddChannelTask' => 'task/plugin',
  'sfPluginBaseTask' => 'task/plugin',
  'sfPluginInstallTask' => 'task/plugin',
  'sfPluginListTask' => 'task/plugin',
  'sfPluginPublishAssetsTask' => 'task/plugin',
  'sfPluginUninstallTask' => 'task/plugin',
  'sfPluginUpgradeTask' => 'task/plugin',
  'sfProjectClearControllersTask' => 'task/project',
  'sfProjectDeployTask' => 'task/project',
  'sfProjectDisableTask' => 'task/project',
  'sfProjectEnableTask' => 'task/project',
  'sfProjectFreezeTask' => 'task/project',
  'sfProjectPermissionsTask' => 'task/project',
  'sfProjectUnfreezeTask' => 'task/project',
  'sfUpgradeTo11Task' => 'task/project',
  'sfUpgradeTo12Task' => 'task/project',
  'sfComponentUpgrade' => 'task/project/upgrade1.1',
  'sfConfigFileUpgrade' => 'task/project/upgrade1.1',
  'sfConfigUpgrade' => 'task/project/upgrade1.1',
  'sfEnvironmentUpgrade' => 'task/project/upgrade1.1',
  'sfFactoriesUpgrade' => 'task/project/upgrade1.1',
  'sfFlashUpgrade' => 'task/project/upgrade1.1',
  'sfLayoutUpgrade' => 'task/project/upgrade1.1',
  'sfLoggerUpgrade' => 'task/project/upgrade1.1',
  'sfPropelUpgrade' => 'task/project/upgrade1.1',
  'sfSettingsUpgrade' => 'task/project/upgrade1.1',
  'sfSingletonUpgrade' => 'task/project/upgrade1.1',
  'sfTestUpgrade' => 'task/project/upgrade1.1',
  'sfUpgrade' => 'task/project/upgrade1.1',
  'sfViewCacheManagerUpgrade' => 'task/project/upgrade1.1',
  'sfWebDebugUpgrade' => 'task/project/upgrade1.1',
  'sfConfigurationUpgrade' => 'task/project/upgrade1.2',
  'sfFactories12Upgrade' => 'task/project/upgrade1.2',
  'sfPluginAssetsUpgrade' => 'task/project/upgrade1.2',
  'sfPropel13Upgrade' => 'task/project/upgrade1.2',
  'sfPropelIniUpgrade' => 'task/project/upgrade1.2',
  'sfBaseTask' => 'task',
  'sfCommandApplicationTask' => 'task',
  'sfFilesystem' => 'task',
  'sfTask' => 'task',
  'sfTestAllTask' => 'task/test',
  'sfTestCoverageTask' => 'task/test',
  'sfTestFunctionalTask' => 'task/test',
  'sfTestUnitTask' => 'task/test',
  'sfTestBrowser' => 'test',
  'sfTestFunctional' => 'test',
  'sfTestFunctionalBase' => 'test',
  'sfTester' => 'test',
  'sfTesterForm' => 'test',
  'sfTesterRequest' => 'test',
  'sfTesterResponse' => 'test',
  'sfTesterUser' => 'test',
  'sfTesterViewCache' => 'test',
  'sfBasicSecurityUser' => 'user',
  'sfSecurityUser' => 'user',
  'sfUser' => 'user',
  'sfBrowser' => 'util',
  'sfBrowserBase' => 'util',
  'sfCallable' => 'util',
  'sfContext' => 'util',
  'sfDomCssSelector' => 'util',
  'sfFinder' => 'util',
  'sfInflector' => 'util',
  'sfNamespacedParameterHolder' => 'util',
  'sfParameterHolder' => 'util',
  'sfToolkit' => 'util',
  'sfValidatorI18nChoiceCountry' => 'validator/i18n',
  'sfValidatorI18nChoiceLanguage' => 'validator/i18n',
  'sfValidatorAnd' => 'validator',
  'sfValidatorBase' => 'validator',
  'sfValidatorBoolean' => 'validator',
  'sfValidatorCSRFToken' => 'validator',
  'sfValidatorCallback' => 'validator',
  'sfValidatorChoice' => 'validator',
  'sfValidatorChoiceMany' => 'validator',
  'sfValidatorDate' => 'validator',
  'sfValidatorDateRange' => 'validator',
  'sfValidatorDateTime' => 'validator',
  'sfValidatorDecorator' => 'validator',
  'sfValidatorEmail' => 'validator',
  'sfValidatorError' => 'validator',
  'sfValidatorErrorSchema' => 'validator',
  'sfValidatorFile' => 'validator',
  'sfValidatorFromDescription' => 'validator',
  'sfValidatorInteger' => 'validator',
  'sfValidatorNumber' => 'validator',
  'sfValidatorOr' => 'validator',
  'sfValidatorPass' => 'validator',
  'sfValidatorRegex' => 'validator',
  'sfValidatorSchema' => 'validator',
  'sfValidatorSchemaCompare' => 'validator',
  'sfValidatorSchemaFilter' => 'validator',
  'sfValidatorSchemaForEach' => 'validator',
  'sfValidatorString' => 'validator',
  'sfValidatorTime' => 'validator',
  'sfValidatorUrl' => 'validator',
  'sfOutputEscaper' => 'view/escaper',
  'sfOutputEscaperArrayDecorator' => 'view/escaper',
  'sfOutputEscaperGetterDecorator' => 'view/escaper',
  'sfOutputEscaperIteratorDecorator' => 'view/escaper',
  'sfOutputEscaperObjectDecorator' => 'view/escaper',
  'sfOutputEscaperSafe' => 'view/escaper',
  'sfPHPView' => 'view',
  'sfPartialView' => 'view',
  'sfView' => 'view',
  'sfViewCacheManager' => 'view',
  'sfViewParameterHolder' => 'view',
  'sfWidgetFormI18nDate' => 'widget/i18n',
  'sfWidgetFormI18nDateTime' => 'widget/i18n',
  'sfWidgetFormI18nSelectCountry' => 'widget/i18n',
  'sfWidgetFormI18nSelectCurrency' => 'widget/i18n',
  'sfWidgetFormI18nSelectLanguage' => 'widget/i18n',
  'sfWidgetFormI18nTime' => 'widget/i18n',
  'sfWidget' => 'widget',
  'sfWidgetForm' => 'widget',
  'sfWidgetFormChoice' => 'widget',
  'sfWidgetFormChoiceMany' => 'widget',
  'sfWidgetFormDate' => 'widget',
  'sfWidgetFormDateRange' => 'widget',
  'sfWidgetFormDateTime' => 'widget',
  'sfWidgetFormFilterDate' => 'widget',
  'sfWidgetFormFilterInput' => 'widget',
  'sfWidgetFormInput' => 'widget',
  'sfWidgetFormInputCheckbox' => 'widget',
  'sfWidgetFormInputFile' => 'widget',
  'sfWidgetFormInputFileEditable' => 'widget',
  'sfWidgetFormInputHidden' => 'widget',
  'sfWidgetFormInputPassword' => 'widget',
  'sfWidgetFormSchema' => 'widget',
  'sfWidgetFormSchemaDecorator' => 'widget',
  'sfWidgetFormSchemaForEach' => 'widget',
  'sfWidgetFormSchemaFormatter' => 'widget',
  'sfWidgetFormSchemaFormatterList' => 'widget',
  'sfWidgetFormSchemaFormatterTable' => 'widget',
  'sfWidgetFormSelect' => 'widget',
  'sfWidgetFormSelectCheckbox' => 'widget',
  'sfWidgetFormSelectMany' => 'widget',
  'sfWidgetFormSelectRadio' => 'widget',
  'sfWidgetFormTextarea' => 'widget',
  'sfWidgetFormTime' => 'widget',
  'sfYaml' => 'yaml',
  'sfYamlDumper' => 'yaml',
  'sfYamlInline' => 'yaml',
  'sfYamlParser' => 'yaml',
);
}
