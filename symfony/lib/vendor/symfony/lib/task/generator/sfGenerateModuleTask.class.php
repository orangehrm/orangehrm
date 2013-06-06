<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/sfGeneratorBaseTask.class.php');

/**
 * Generates a new module.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfGenerateModuleTask.class.php 23922 2009-11-14 14:58:38Z fabien $
 */
class sfGenerateModuleTask extends sfGeneratorBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
      new sfCommandArgument('module', sfCommandArgument::REQUIRED, 'The module name'),
    ));

    $this->namespace = 'generate';
    $this->name = 'module';

    $this->briefDescription = 'Generates a new module';

    $this->detailedDescription = <<<EOF
The [generate:module|INFO] task creates the basic directory structure
for a new module in an existing application:

  [./symfony generate:module frontend article|INFO]

The task can also change the author name found in the [actions.class.php|COMMENT]
if you have configure it in [config/properties.ini|COMMENT]:

  [[symfony]
    name=blog
    author=Fabien Potencier <fabien.potencier@sensio.com>|INFO]

You can customize the default skeleton used by the task by creating a
[%sf_data_dir%/skeleton/module|COMMENT] directory.

The task also creates a functional test stub named
[%sf_test_dir%/functional/%application%/%module%ActionsTest.class.php|COMMENT]
that does not pass by default.

If a module with the same name already exists in the application,
it throws a [sfCommandException|COMMENT].
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $app    = $arguments['application'];
    $module = $arguments['module'];

    // Validate the module name
    if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $module))
    {
      throw new sfCommandException(sprintf('The module name "%s" is invalid.', $module));
    }

    $moduleDir = sfConfig::get('sf_app_module_dir').'/'.$module;

    if (is_dir($moduleDir))
    {
      throw new sfCommandException(sprintf('The module "%s" already exists in the "%s" application.', $moduleDir, $app));
    }

    $properties = parse_ini_file(sfConfig::get('sf_config_dir').'/properties.ini', true);

    $constants = array(
      'PROJECT_NAME' => isset($properties['symfony']['name']) ? $properties['symfony']['name'] : 'symfony',
      'APP_NAME'     => $app,
      'MODULE_NAME'  => $module,
      'AUTHOR_NAME'  => isset($properties['symfony']['author']) ? $properties['symfony']['author'] : 'Your name here',
    );

    if (is_readable(sfConfig::get('sf_data_dir').'/skeleton/module'))
    {
      $skeletonDir = sfConfig::get('sf_data_dir').'/skeleton/module';
    }
    else
    {
      $skeletonDir = dirname(__FILE__).'/skeleton/module';
    }

    // create basic application structure
    $finder = sfFinder::type('any')->discard('.sf');
    $this->getFilesystem()->mirror($skeletonDir.'/module', $moduleDir, $finder);

    // create basic test
    $this->getFilesystem()->copy($skeletonDir.'/test/actionsTest.php', sfConfig::get('sf_test_dir').'/functional/'.$app.'/'.$module.'ActionsTest.php');

    // customize test file
    $this->getFilesystem()->replaceTokens(sfConfig::get('sf_test_dir').'/functional/'.$app.DIRECTORY_SEPARATOR.$module.'ActionsTest.php', '##', '##', $constants);

    // customize php and yml files
    $finder = sfFinder::type('file')->name('*.php', '*.yml');
    $this->getFilesystem()->replaceTokens($finder->in($moduleDir), '##', '##', $constants);
  }
}
