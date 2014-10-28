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
define('SYMFONY_VERSION', '1.4.18');

/**
 * sfCoreAutoload class.
 *
 * This class is a singleton as PHP seems to be unable to register 2 autoloaders that are instances
 * of the same class (why?).
 *
 * @package    symfony
 * @subpackage autoload
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfCoreAutoload.class.php 32415 2011-03-30 16:09:00Z Kris.Wallsmith
 $
 */
class sfCoreAutoload
{
  static protected
    $registered = false,
    $instance   = null;

  protected
    $baseDir = null;

  protected function __construct()
  {
    $this->baseDir = realpath(dirname(__FILE__).'/..');
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
   * @param  string  $class  A class name.
   *
   * @return boolean Returns true if the class has been loaded
   */
  public function autoload($class)
  {
    if ($path = $this->getClassPath($class))
    {
      require $path;

      return true;
    }

    return false;
  }

  /**
   * Returns the filename of the supplied class.
   *
   * @param  string $class The class name (case insensitive)
   *
   * @return string|null An absolute path or null
   */
  public function getClassPath($class)
  {
    $class = strtolower($class);

    if (!isset($this->classes[$class]))
    {
      return null;
    }

    return $this->baseDir.'/'.$this->classes[$class];
  }

  /**
   * Returns the base directory this autoloader is working on.
   *
   * @return string The path to the symfony core lib directory
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
      ->prune('helper')
      ->name('*.php')
      ->in($libDir)
    ;

    sort($files, SORT_STRING);

    $classes = '';
    foreach ($files as $file)
    {
      $file  = str_replace(DIRECTORY_SEPARATOR, '/', $file);
      $class = basename($file, false === strpos($file, '.class.php') ? '.php' : '.class.php');

      $contents = file_get_contents($file);
      if (false !== stripos($contents, 'class '.$class) || false !== stripos($contents, 'interface '.$class))
      {
        $classes .= sprintf("    '%s' => '%s',\n", strtolower($class), substr(str_replace($libDir, '', $file), 1));
      }
    }

    $content = preg_replace('/protected \$classes = array *\(.*?\);/s', sprintf("protected \$classes = array(\n%s  );", $classes), file_get_contents(__FILE__));

    file_put_contents(__FILE__, $content);
  }

  // Don't edit this property by hand.
  // To update it, use sfCoreAutoload::make()
  protected $classes = array(
    'sfaction' => 'action/sfAction.class.php',
    'sfactionstack' => 'action/sfActionStack.class.php',
    'sfactionstackentry' => 'action/sfActionStackEntry.class.php',
    'sfactions' => 'action/sfActions.class.php',
    'sfcomponent' => 'action/sfComponent.class.php',
    'sfcomponents' => 'action/sfComponents.class.php',
    'sfdata' => 'addon/sfData.class.php',
    'sfpager' => 'addon/sfPager.class.php',
    'sfautoload' => 'autoload/sfAutoload.class.php',
    'sfautoloadagain' => 'autoload/sfAutoloadAgain.class.php',
    'sfcoreautoload' => 'autoload/sfCoreAutoload.class.php',
    'sfsimpleautoload' => 'autoload/sfSimpleAutoload.class.php',
    'sfapccache' => 'cache/sfAPCCache.class.php',
    'sfcache' => 'cache/sfCache.class.php',
    'sfeacceleratorcache' => 'cache/sfEAcceleratorCache.class.php',
    'sffilecache' => 'cache/sfFileCache.class.php',
    'sffunctioncache' => 'cache/sfFunctionCache.class.php',
    'sfmemcachecache' => 'cache/sfMemcacheCache.class.php',
    'sfnocache' => 'cache/sfNoCache.class.php',
    'sfsqlitecache' => 'cache/sfSQLiteCache.class.php',
    'sfxcachecache' => 'cache/sfXCacheCache.class.php',
    'sfansicolorformatter' => 'command/sfAnsiColorFormatter.class.php',
    'sfcommandapplication' => 'command/sfCommandApplication.class.php',
    'sfcommandargument' => 'command/sfCommandArgument.class.php',
    'sfcommandargumentset' => 'command/sfCommandArgumentSet.class.php',
    'sfcommandargumentsexception' => 'command/sfCommandArgumentsException.class.php',
    'sfcommandexception' => 'command/sfCommandException.class.php',
    'sfcommandlogger' => 'command/sfCommandLogger.class.php',
    'sfcommandmanager' => 'command/sfCommandManager.class.php',
    'sfcommandoption' => 'command/sfCommandOption.class.php',
    'sfcommandoptionset' => 'command/sfCommandOptionSet.class.php',
    'sfformatter' => 'command/sfFormatter.class.php',
    'sfsymfonycommandapplication' => 'command/sfSymfonyCommandApplication.class.php',
    'sfapplicationconfiguration' => 'config/sfApplicationConfiguration.class.php',
    'sfautoloadconfighandler' => 'config/sfAutoloadConfigHandler.class.php',
    'sfcacheconfighandler' => 'config/sfCacheConfigHandler.class.php',
    'sfcompileconfighandler' => 'config/sfCompileConfigHandler.class.php',
    'sfconfig' => 'config/sfConfig.class.php',
    'sfconfigcache' => 'config/sfConfigCache.class.php',
    'sfconfighandler' => 'config/sfConfigHandler.class.php',
    'sfdatabaseconfighandler' => 'config/sfDatabaseConfigHandler.class.php',
    'sfdefineenvironmentconfighandler' => 'config/sfDefineEnvironmentConfigHandler.class.php',
    'sffactoryconfighandler' => 'config/sfFactoryConfigHandler.class.php',
    'sffilterconfighandler' => 'config/sfFilterConfigHandler.class.php',
    'sfgeneratorconfighandler' => 'config/sfGeneratorConfigHandler.class.php',
    'sfpluginconfiguration' => 'config/sfPluginConfiguration.class.php',
    'sfpluginconfigurationgeneric' => 'config/sfPluginConfigurationGeneric.class.php',
    'sfprojectconfiguration' => 'config/sfProjectConfiguration.class.php',
    'sfrootconfighandler' => 'config/sfRootConfigHandler.class.php',
    'sfroutingconfighandler' => 'config/sfRoutingConfigHandler.class.php',
    'sfsecurityconfighandler' => 'config/sfSecurityConfigHandler.class.php',
    'sfsimpleyamlconfighandler' => 'config/sfSimpleYamlConfigHandler.class.php',
    'sfviewconfighandler' => 'config/sfViewConfigHandler.class.php',
    'sfyamlconfighandler' => 'config/sfYamlConfigHandler.class.php',
    'sfcontroller' => 'controller/sfController.class.php',
    'sffrontwebcontroller' => 'controller/sfFrontWebController.class.php',
    'sfwebcontroller' => 'controller/sfWebController.class.php',
    'sfdatabase' => 'database/sfDatabase.class.php',
    'sfdatabasemanager' => 'database/sfDatabaseManager.class.php',
    'sfmysqldatabase' => 'database/sfMySQLDatabase.class.php',
    'sfmysqlidatabase' => 'database/sfMySQLiDatabase.class.php',
    'sfpdodatabase' => 'database/sfPDODatabase.class.php',
    'sfpostgresqldatabase' => 'database/sfPostgreSQLDatabase.class.php',
    'sfdebug' => 'debug/sfDebug.class.php',
    'sftimer' => 'debug/sfTimer.class.php',
    'sftimermanager' => 'debug/sfTimerManager.class.php',
    'sfwebdebug' => 'debug/sfWebDebug.class.php',
    'sfwebdebugpanel' => 'debug/sfWebDebugPanel.class.php',
    'sfwebdebugpanelcache' => 'debug/sfWebDebugPanelCache.class.php',
    'sfwebdebugpanelconfig' => 'debug/sfWebDebugPanelConfig.class.php',
    'sfwebdebugpanellogs' => 'debug/sfWebDebugPanelLogs.class.php',
    'sfwebdebugpanelmailer' => 'debug/sfWebDebugPanelMailer.class.php',
    'sfwebdebugpanelmemory' => 'debug/sfWebDebugPanelMemory.class.php',
    'sfwebdebugpanelsymfonyversion' => 'debug/sfWebDebugPanelSymfonyVersion.class.php',
    'sfwebdebugpaneltimer' => 'debug/sfWebDebugPanelTimer.class.php',
    'sfwebdebugpanelview' => 'debug/sfWebDebugPanelView.class.php',
    'sfoutputescaper' => 'escaper/sfOutputEscaper.class.php',
    'sfoutputescaperarraydecorator' => 'escaper/sfOutputEscaperArrayDecorator.class.php',
    'sfoutputescapergetterdecorator' => 'escaper/sfOutputEscaperGetterDecorator.class.php',
    'sfoutputescaperiteratordecorator' => 'escaper/sfOutputEscaperIteratorDecorator.class.php',
    'sfoutputescaperobjectdecorator' => 'escaper/sfOutputEscaperObjectDecorator.class.php',
    'sfoutputescapersafe' => 'escaper/sfOutputEscaperSafe.class.php',
    'sfevent' => 'event_dispatcher/sfEvent.php',
    'sfeventdispatcher' => 'event_dispatcher/sfEventDispatcher.php',
    'sfcacheexception' => 'exception/sfCacheException.class.php',
    'sfconfigurationexception' => 'exception/sfConfigurationException.class.php',
    'sfcontrollerexception' => 'exception/sfControllerException.class.php',
    'sfdatabaseexception' => 'exception/sfDatabaseException.class.php',
    'sferror404exception' => 'exception/sfError404Exception.class.php',
    'sfexception' => 'exception/sfException.class.php',
    'sffactoryexception' => 'exception/sfFactoryException.class.php',
    'sffileexception' => 'exception/sfFileException.class.php',
    'sffilterexception' => 'exception/sfFilterException.class.php',
    'sfforwardexception' => 'exception/sfForwardException.class.php',
    'sfinitializationexception' => 'exception/sfInitializationException.class.php',
    'sfparseexception' => 'exception/sfParseException.class.php',
    'sfrenderexception' => 'exception/sfRenderException.class.php',
    'sfsecurityexception' => 'exception/sfSecurityException.class.php',
    'sfstopexception' => 'exception/sfStopException.class.php',
    'sfstorageexception' => 'exception/sfStorageException.class.php',
    'sfviewexception' => 'exception/sfViewException.class.php',
    'sfbasicsecurityfilter' => 'filter/sfBasicSecurityFilter.class.php',
    'sfcachefilter' => 'filter/sfCacheFilter.class.php',
    'sfcommonfilter' => 'filter/sfCommonFilter.class.php',
    'sfexecutionfilter' => 'filter/sfExecutionFilter.class.php',
    'sffilter' => 'filter/sfFilter.class.php',
    'sffilterchain' => 'filter/sfFilterChain.class.php',
    'sfrenderingfilter' => 'filter/sfRenderingFilter.class.php',
    'sfformfilter' => 'form/addon/sfFormFilter.class.php',
    'sfformobject' => 'form/addon/sfFormObject.class.php',
    'sfformsymfony' => 'form/addon/sfFormSymfony.class.php',
    'sfform' => 'form/sfForm.class.php',
    'sfformfield' => 'form/sfFormField.class.php',
    'sfformfieldschema' => 'form/sfFormFieldSchema.class.php',
    'sfgenerator' => 'generator/sfGenerator.class.php',
    'sfgeneratormanager' => 'generator/sfGeneratorManager.class.php',
    'sfmodelgenerator' => 'generator/sfModelGenerator.class.php',
    'sfmodelgeneratorconfiguration' => 'generator/sfModelGeneratorConfiguration.class.php',
    'sfmodelgeneratorconfigurationfield' => 'generator/sfModelGeneratorConfigurationField.class.php',
    'sfmodelgeneratorhelper' => 'generator/sfModelGeneratorHelper.class.php',
    'tgettext' => 'i18n/Gettext/TGettext.class.php',
    'sfi18napplicationextract' => 'i18n/extract/sfI18nApplicationExtract.class.php',
    'sfi18nextract' => 'i18n/extract/sfI18nExtract.class.php',
    'sfi18nextractorinterface' => 'i18n/extract/sfI18nExtractorInterface.class.php',
    'sfi18nmoduleextract' => 'i18n/extract/sfI18nModuleExtract.class.php',
    'sfi18nphpextractor' => 'i18n/extract/sfI18nPhpExtractor.class.php',
    'sfi18nyamlextractor' => 'i18n/extract/sfI18nYamlExtractor.class.php',
    'sfi18nyamlgeneratorextractor' => 'i18n/extract/sfI18nYamlGeneratorExtractor.class.php',
    'sfi18nyamlvalidateextractor' => 'i18n/extract/sfI18nYamlValidateExtractor.class.php',
    'sfchoiceformat' => 'i18n/sfChoiceFormat.class.php',
    'sfcultureinfo' => 'i18n/sfCultureInfo.class.php',
    'sfdateformat' => 'i18n/sfDateFormat.class.php',
    'sfdatetimeformatinfo' => 'i18n/sfDateTimeFormatInfo.class.php',
    'sfi18n' => 'i18n/sfI18N.class.php',
    'sfimessagesource' => 'i18n/sfIMessageSource.class.php',
    'sfmessageformat' => 'i18n/sfMessageFormat.class.php',
    'sfmessagesource' => 'i18n/sfMessageSource.class.php',
    'sfmessagesource_aggregate' => 'i18n/sfMessageSource_Aggregate.class.php',
    'sfmessagesource_database' => 'i18n/sfMessageSource_Database.class.php',
    'sfmessagesource_file' => 'i18n/sfMessageSource_File.class.php',
    'sfmessagesource_mysql' => 'i18n/sfMessageSource_MySQL.class.php',
    'sfmessagesource_sqlite' => 'i18n/sfMessageSource_SQLite.class.php',
    'sfmessagesource_xliff' => 'i18n/sfMessageSource_XLIFF.class.php',
    'sfmessagesource_gettext' => 'i18n/sfMessageSource_gettext.class.php',
    'sfnumberformat' => 'i18n/sfNumberFormat.class.php',
    'sfnumberformatinfo' => 'i18n/sfNumberFormatInfo.class.php',
    'sfaggregatelogger' => 'log/sfAggregateLogger.class.php',
    'sfconsolelogger' => 'log/sfConsoleLogger.class.php',
    'sffilelogger' => 'log/sfFileLogger.class.php',
    'sflogger' => 'log/sfLogger.class.php',
    'sfloggerinterface' => 'log/sfLoggerInterface.class.php',
    'sfloggerwrapper' => 'log/sfLoggerWrapper.class.php',
    'sfnologger' => 'log/sfNoLogger.class.php',
    'sfstreamlogger' => 'log/sfStreamLogger.class.php',
    'sfvarlogger' => 'log/sfVarLogger.class.php',
    'sfwebdebuglogger' => 'log/sfWebDebugLogger.class.php',
    'sfmailer' => 'mailer/sfMailer.class.php',
    'sfmailermessageloggerplugin' => 'mailer/sfMailerMessageLoggerPlugin.class.php',
    'sfpearconfig' => 'plugin/sfPearConfig.class.php',
    'sfpeardownloader' => 'plugin/sfPearDownloader.class.php',
    'sfpearenvironment' => 'plugin/sfPearEnvironment.class.php',
    'sfpearfrontendplugin' => 'plugin/sfPearFrontendPlugin.class.php',
    'sfpearrest' => 'plugin/sfPearRest.class.php',
    'sfpearrest10' => 'plugin/sfPearRest10.class.php',
    'sfpearrest11' => 'plugin/sfPearRest11.class.php',
    'sfpearrestplugin' => 'plugin/sfPearRestPlugin.class.php',
    'sfplugindependencyexception' => 'plugin/sfPluginDependencyException.class.php',
    'sfpluginexception' => 'plugin/sfPluginException.class.php',
    'sfpluginmanager' => 'plugin/sfPluginManager.class.php',
    'sfpluginrecursivedependencyexception' => 'plugin/sfPluginRecursiveDependencyException.class.php',
    'sfpluginrestexception' => 'plugin/sfPluginRestException.class.php',
    'sfsymfonypluginmanager' => 'plugin/sfSymfonyPluginManager.class.php',
    'sfrequest' => 'request/sfRequest.class.php',
    'sfwebrequest' => 'request/sfWebRequest.class.php',
    'sfresponse' => 'response/sfResponse.class.php',
    'sfwebresponse' => 'response/sfWebResponse.class.php',
    'sfobjectroute' => 'routing/sfObjectRoute.class.php',
    'sfobjectroutecollection' => 'routing/sfObjectRouteCollection.class.php',
    'sfpatternrouting' => 'routing/sfPatternRouting.class.php',
    'sfrequestroute' => 'routing/sfRequestRoute.class.php',
    'sfroute' => 'routing/sfRoute.class.php',
    'sfroutecollection' => 'routing/sfRouteCollection.class.php',
    'sfrouting' => 'routing/sfRouting.class.php',
    'sfcachesessionstorage' => 'storage/sfCacheSessionStorage.class.php',
    'sfdatabasesessionstorage' => 'storage/sfDatabaseSessionStorage.class.php',
    'sfmysqlsessionstorage' => 'storage/sfMySQLSessionStorage.class.php',
    'sfmysqlisessionstorage' => 'storage/sfMySQLiSessionStorage.class.php',
    'sfnostorage' => 'storage/sfNoStorage.class.php',
    'sfpdosessionstorage' => 'storage/sfPDOSessionStorage.class.php',
    'sfpostgresqlsessionstorage' => 'storage/sfPostgreSQLSessionStorage.class.php',
    'sfsessionstorage' => 'storage/sfSessionStorage.class.php',
    'sfsessionteststorage' => 'storage/sfSessionTestStorage.class.php',
    'sfstorage' => 'storage/sfStorage.class.php',
    'sfapproutestask' => 'task/app/sfAppRoutesTask.class.php',
    'sfcachecleartask' => 'task/cache/sfCacheClearTask.class.php',
    'sfconfigureauthortask' => 'task/configure/sfConfigureAuthorTask.class.php',
    'sfgenerateapptask' => 'task/generator/sfGenerateAppTask.class.php',
    'sfgeneratemoduletask' => 'task/generator/sfGenerateModuleTask.class.php',
    'sfgenerateprojecttask' => 'task/generator/sfGenerateProjectTask.class.php',
    'sfgeneratetasktask' => 'task/generator/sfGenerateTaskTask.class.php',
    'sfgeneratorbasetask' => 'task/generator/sfGeneratorBaseTask.class.php',
    'sfhelptask' => 'task/help/sfHelpTask.class.php',
    'sflisttask' => 'task/help/sfListTask.class.php',
    'sfi18nextracttask' => 'task/i18n/sfI18nExtractTask.class.php',
    'sfi18nfindtask' => 'task/i18n/sfI18nFindTask.class.php',
    'sflogcleartask' => 'task/log/sfLogClearTask.class.php',
    'sflogrotatetask' => 'task/log/sfLogRotateTask.class.php',
    'sfpluginaddchanneltask' => 'task/plugin/sfPluginAddChannelTask.class.php',
    'sfpluginbasetask' => 'task/plugin/sfPluginBaseTask.class.php',
    'sfplugininstalltask' => 'task/plugin/sfPluginInstallTask.class.php',
    'sfpluginlisttask' => 'task/plugin/sfPluginListTask.class.php',
    'sfpluginpublishassetstask' => 'task/plugin/sfPluginPublishAssetsTask.class.php',
    'sfpluginuninstalltask' => 'task/plugin/sfPluginUninstallTask.class.php',
    'sfpluginupgradetask' => 'task/plugin/sfPluginUpgradeTask.class.php',
    'sfprojectclearcontrollerstask' => 'task/project/sfProjectClearControllersTask.class.php',
    'sfprojectdeploytask' => 'task/project/sfProjectDeployTask.class.php',
    'sfprojectdisabletask' => 'task/project/sfProjectDisableTask.class.php',
    'sfprojectenabletask' => 'task/project/sfProjectEnableTask.class.php',
    'sfprojectoptimizetask' => 'task/project/sfProjectOptimizeTask.class.php',
    'sfprojectpermissionstask' => 'task/project/sfProjectPermissionsTask.class.php',
    'sfprojectsendemailstask' => 'task/project/sfProjectSendEmailsTask.class.php',
    'sfdeprecatedclassesvalidation' => 'task/project/validation/sfDeprecatedClassesValidation.class.php',
    'sfdeprecatedconfigurationfilesvalidation' => 'task/project/validation/sfDeprecatedConfigurationFilesValidation.class.php',
    'sfdeprecatedhelpersvalidation' => 'task/project/validation/sfDeprecatedHelpersValidation.class.php',
    'sfdeprecatedmethodsvalidation' => 'task/project/validation/sfDeprecatedMethodsValidation.class.php',
    'sfdeprecatedpluginsvalidation' => 'task/project/validation/sfDeprecatedPluginsValidation.class.php',
    'sfdeprecatedsettingsvalidation' => 'task/project/validation/sfDeprecatedSettingsValidation.class.php',
    'sfparameterholdervalidation' => 'task/project/validation/sfParameterHolderValidation.class.php',
    'sfvalidation' => 'task/project/validation/sfValidation.class.php',
    'sfbasetask' => 'task/sfBaseTask.class.php',
    'sfcommandapplicationtask' => 'task/sfCommandApplicationTask.class.php',
    'sffilesystem' => 'task/sfFilesystem.class.php',
    'sftask' => 'task/sfTask.class.php',
    'lime_symfony' => 'task/symfony/lime_symfony.php',
    'sfsymfonytesttask' => 'task/symfony/sfSymfonyTestTask.class.php',
    'sflimeharness' => 'task/test/sfLimeHarness.class.php',
    'sftestalltask' => 'task/test/sfTestAllTask.class.php',
    'sftestbasetask' => 'task/test/sfTestBaseTask.class.php',
    'sftestcoveragetask' => 'task/test/sfTestCoverageTask.class.php',
    'sftestfunctionaltask' => 'task/test/sfTestFunctionalTask.class.php',
    'sftestunittask' => 'task/test/sfTestUnitTask.class.php',
    'sftestbrowser' => 'test/sfTestBrowser.class.php',
    'sftestfunctional' => 'test/sfTestFunctional.class.php',
    'sftestfunctionalbase' => 'test/sfTestFunctionalBase.class.php',
    'sftester' => 'test/sfTester.class.php',
    'sftesterform' => 'test/sfTesterForm.class.php',
    'sftestermailer' => 'test/sfTesterMailer.class.php',
    'sftesterrequest' => 'test/sfTesterRequest.class.php',
    'sftesterresponse' => 'test/sfTesterResponse.class.php',
    'sftesteruser' => 'test/sfTesterUser.class.php',
    'sftesterviewcache' => 'test/sfTesterViewCache.class.php',
    'sfbasicsecurityuser' => 'user/sfBasicSecurityUser.class.php',
    'sfsecurityuser' => 'user/sfSecurityUser.class.php',
    'sfuser' => 'user/sfUser.class.php',
    'sfbrowser' => 'util/sfBrowser.class.php',
    'sfbrowserbase' => 'util/sfBrowserBase.class.php',
    'sfcallable' => 'util/sfCallable.class.php',
    'sfclassmanipulator' => 'util/sfClassManipulator.class.php',
    'sfcontext' => 'util/sfContext.class.php',
    'sfdomcssselector' => 'util/sfDomCssSelector.class.php',
    'sffinder' => 'util/sfFinder.class.php',
    'sfinflector' => 'util/sfInflector.class.php',
    'sfnamespacedparameterholder' => 'util/sfNamespacedParameterHolder.class.php',
    'sfparameterholder' => 'util/sfParameterHolder.class.php',
    'sftoolkit' => 'util/sfToolkit.class.php',
    'sfvalidatori18nchoicecountry' => 'validator/i18n/sfValidatorI18nChoiceCountry.class.php',
    'sfvalidatori18nchoicelanguage' => 'validator/i18n/sfValidatorI18nChoiceLanguage.class.php',
    'sfvalidatori18nchoicetimezone' => 'validator/i18n/sfValidatorI18nChoiceTimezone.class.php',
    'sfvalidatedfile' => 'validator/sfValidatedFile.class.php',
    'sfvalidatorand' => 'validator/sfValidatorAnd.class.php',
    'sfvalidatorbase' => 'validator/sfValidatorBase.class.php',
    'sfvalidatorboolean' => 'validator/sfValidatorBoolean.class.php',
    'sfvalidatorcsrftoken' => 'validator/sfValidatorCSRFToken.class.php',
    'sfvalidatorcallback' => 'validator/sfValidatorCallback.class.php',
    'sfvalidatorchoice' => 'validator/sfValidatorChoice.class.php',
    'sfvalidatordate' => 'validator/sfValidatorDate.class.php',
    'sfvalidatordaterange' => 'validator/sfValidatorDateRange.class.php',
    'sfvalidatordatetime' => 'validator/sfValidatorDateTime.class.php',
    'sfvalidatordecorator' => 'validator/sfValidatorDecorator.class.php',
    'sfvalidatoremail' => 'validator/sfValidatorEmail.class.php',
    'sfvalidatorerror' => 'validator/sfValidatorError.class.php',
    'sfvalidatorerrorschema' => 'validator/sfValidatorErrorSchema.class.php',
    'sfvalidatorfile' => 'validator/sfValidatorFile.class.php',
    'sfvalidatorfromdescription' => 'validator/sfValidatorFromDescription.class.php',
    'sfvalidatorinteger' => 'validator/sfValidatorInteger.class.php',
    'sfvalidatornumber' => 'validator/sfValidatorNumber.class.php',
    'sfvalidatoror' => 'validator/sfValidatorOr.class.php',
    'sfvalidatorpass' => 'validator/sfValidatorPass.class.php',
    'sfvalidatorregex' => 'validator/sfValidatorRegex.class.php',
    'sfvalidatorschema' => 'validator/sfValidatorSchema.class.php',
    'sfvalidatorschemacompare' => 'validator/sfValidatorSchemaCompare.class.php',
    'sfvalidatorschemafilter' => 'validator/sfValidatorSchemaFilter.class.php',
    'sfvalidatorschemaforeach' => 'validator/sfValidatorSchemaForEach.class.php',
    'sfvalidatorstring' => 'validator/sfValidatorString.class.php',
    'sfvalidatortime' => 'validator/sfValidatorTime.class.php',
    'sfvalidatorurl' => 'validator/sfValidatorUrl.class.php',
    'sfphpview' => 'view/sfPHPView.class.php',
    'sfpartialview' => 'view/sfPartialView.class.php',
    'sfview' => 'view/sfView.class.php',
    'sfviewcachemanager' => 'view/sfViewCacheManager.class.php',
    'sfviewparameterholder' => 'view/sfViewParameterHolder.class.php',
    'sfwidgetformi18nchoicecountry' => 'widget/i18n/sfWidgetFormI18nChoiceCountry.class.php',
    'sfwidgetformi18nchoicecurrency' => 'widget/i18n/sfWidgetFormI18nChoiceCurrency.class.php',
    'sfwidgetformi18nchoicelanguage' => 'widget/i18n/sfWidgetFormI18nChoiceLanguage.class.php',
    'sfwidgetformi18nchoicetimezone' => 'widget/i18n/sfWidgetFormI18nChoiceTimezone.class.php',
    'sfwidgetformi18ndate' => 'widget/i18n/sfWidgetFormI18nDate.class.php',
    'sfwidgetformi18ndatetime' => 'widget/i18n/sfWidgetFormI18nDateTime.class.php',
    'sfwidgetformi18ntime' => 'widget/i18n/sfWidgetFormI18nTime.class.php',
    'sfwidget' => 'widget/sfWidget.class.php',
    'sfwidgetform' => 'widget/sfWidgetForm.class.php',
    'sfwidgetformchoice' => 'widget/sfWidgetFormChoice.class.php',
    'sfwidgetformchoicebase' => 'widget/sfWidgetFormChoiceBase.class.php',
    'sfwidgetformdate' => 'widget/sfWidgetFormDate.class.php',
    'sfwidgetformdaterange' => 'widget/sfWidgetFormDateRange.class.php',
    'sfwidgetformdatetime' => 'widget/sfWidgetFormDateTime.class.php',
    'sfwidgetformfilterdate' => 'widget/sfWidgetFormFilterDate.class.php',
    'sfwidgetformfilterinput' => 'widget/sfWidgetFormFilterInput.class.php',
    'sfwidgetforminput' => 'widget/sfWidgetFormInput.class.php',
    'sfwidgetforminputcheckbox' => 'widget/sfWidgetFormInputCheckbox.class.php',
    'sfwidgetforminputfile' => 'widget/sfWidgetFormInputFile.class.php',
    'sfwidgetforminputfileeditable' => 'widget/sfWidgetFormInputFileEditable.class.php',
    'sfwidgetforminputhidden' => 'widget/sfWidgetFormInputHidden.class.php',
    'sfwidgetforminputpassword' => 'widget/sfWidgetFormInputPassword.class.php',
    'sfwidgetforminputtext' => 'widget/sfWidgetFormInputText.class.php',
    'sfwidgetformschema' => 'widget/sfWidgetFormSchema.class.php',
    'sfwidgetformschemadecorator' => 'widget/sfWidgetFormSchemaDecorator.class.php',
    'sfwidgetformschemaforeach' => 'widget/sfWidgetFormSchemaForEach.class.php',
    'sfwidgetformschemaformatter' => 'widget/sfWidgetFormSchemaFormatter.class.php',
    'sfwidgetformschemaformatterlist' => 'widget/sfWidgetFormSchemaFormatterList.class.php',
    'sfwidgetformschemaformattertable' => 'widget/sfWidgetFormSchemaFormatterTable.class.php',
    'sfwidgetformselect' => 'widget/sfWidgetFormSelect.class.php',
    'sfwidgetformselectcheckbox' => 'widget/sfWidgetFormSelectCheckbox.class.php',
    'sfwidgetformselectmany' => 'widget/sfWidgetFormSelectMany.class.php',
    'sfwidgetformselectradio' => 'widget/sfWidgetFormSelectRadio.class.php',
    'sfwidgetformtextarea' => 'widget/sfWidgetFormTextarea.class.php',
    'sfwidgetformtime' => 'widget/sfWidgetFormTime.class.php',
    'sfyaml' => 'yaml/sfYaml.php',
    'sfyamldumper' => 'yaml/sfYamlDumper.php',
    'sfyamlinline' => 'yaml/sfYamlInline.php',
    'sfyamlparser' => 'yaml/sfYamlParser.php',
  );
}
