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
 * Generates a new project.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfGenerateProjectTask.class.php 30530 2010-08-04 16:38:41Z fabien $
 */
class sfGenerateProjectTask extends sfGeneratorBaseTask
{
  /**
   * @see sfTask
   */
  protected function doRun(sfCommandManager $commandManager, $options)
  {
    $this->process($commandManager, $options);

    return $this->execute($commandManager->getArgumentValues(), $commandManager->getOptionValues());
  }

  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('name', sfCommandArgument::REQUIRED, 'The project name'),
      new sfCommandArgument('author', sfCommandArgument::OPTIONAL, 'The project author', 'Your name here'),
    ));

    $this->addOptions(array(
      new sfCommandOption('orm', null, sfCommandOption::PARAMETER_REQUIRED, 'The ORM to use by default', 'Doctrine'),
      new sfCommandOption('installer', null, sfCommandOption::PARAMETER_REQUIRED, 'An installer script to execute', null),
    ));

    $this->namespace = 'generate';
    $this->name = 'project';

    $this->briefDescription = 'Generates a new project';

    $this->detailedDescription = <<<EOF
The [generate:project|INFO] task creates the basic directory structure
for a new project in the current directory:

  [./symfony generate:project blog|INFO]

If the current directory already contains a symfony project,
it throws a [sfCommandException|COMMENT].

By default, the task configures Doctrine as the ORM. If you want to use
Propel, use the [--orm|COMMENT] option:

  [./symfony generate:project blog --orm=Propel|INFO]

If you don't want to use an ORM, pass [none|COMMENT] to [--orm|COMMENT] option:

  [./symfony generate:project blog --orm=none|INFO]

You can also pass the [--installer|COMMENT] option to further customize the
project:

  [./symfony generate:project blog --installer=./installer.php|INFO]

You can optionally include a second [author|COMMENT] argument to specify what name to
use as author when symfony generates new classes:

  [./symfony generate:project blog "Jack Doe"|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    if (file_exists('symfony'))
    {
      throw new sfCommandException(sprintf('A symfony project already exists in this directory (%s).', getcwd()));
    }

    if (!in_array(strtolower($options['orm']), array('propel', 'doctrine', 'none')))
    {
      throw new InvalidArgumentException(sprintf('Invalid ORM name "%s".', $options['orm']));
    }

    if ($options['installer'] && $this->commandApplication && !file_exists($options['installer']))
    {
      throw new InvalidArgumentException(sprintf('The installer "%s" does not exist.', $options['installer']));
    }

    // clean orm option
    $options['orm'] = ucfirst(strtolower($options['orm']));

    $this->arguments = $arguments;
    $this->options = $options;

    // create basic project structure
    $this->installDir(dirname(__FILE__).'/skeleton/project');

    // update ProjectConfiguration class (use a relative path when the symfony core is nested within the project)
    $symfonyCoreAutoload = 0 === strpos(sfConfig::get('sf_symfony_lib_dir'), sfConfig::get('sf_root_dir')) ?
      sprintf('dirname(__FILE__).\'/..%s/autoload/sfCoreAutoload.class.php\'', str_replace(sfConfig::get('sf_root_dir'), '', sfConfig::get('sf_symfony_lib_dir'))) :
      var_export(sfConfig::get('sf_symfony_lib_dir').'/autoload/sfCoreAutoload.class.php', true);

    $this->replaceTokens(array(sfConfig::get('sf_config_dir')), array('SYMFONY_CORE_AUTOLOAD' => str_replace('\\', '/', $symfonyCoreAutoload)));

    $this->tokens = array(
      'ORM'          => $this->options['orm'],
      'PROJECT_NAME' => $this->arguments['name'],
      'AUTHOR_NAME'  => $this->arguments['author'],
      'PROJECT_DIR'  => sfConfig::get('sf_root_dir'),
    );

    $this->replaceTokens();

    // execute the choosen ORM installer script
    if (in_array($options['orm'], array('Doctrine', 'Propel')))
    {
      include dirname(__FILE__).'/../../plugins/sf'.$options['orm'].'Plugin/config/installer.php';
    }

    // execute a custom installer
    if ($options['installer'] && $this->commandApplication)
    {
      if ($this->canRunInstaller($options['installer']))
      {
        $this->reloadTasks();
        include $options['installer'];
      }
    }

    // fix permission for common directories
    $fixPerms = new sfProjectPermissionsTask($this->dispatcher, $this->formatter);
    $fixPerms->setCommandApplication($this->commandApplication);
    $fixPerms->setConfiguration($this->configuration);
    $fixPerms->run();

    $this->replaceTokens();
  }

  protected function canRunInstaller($installer)
  {
    if (preg_match('#^(https?|ftps?)://#', $installer))
    {
      if (ini_get('allow_url_fopen') === false)
      {
        $this->logSection('generate', sprintf('Cannot run remote installer "%s" because "allow_url_fopen" is off', $installer));
      }
      if (ini_get('allow_url_include') === false)
      {
        $this->logSection('generate', sprintf('Cannot run remote installer "%s" because "allow_url_include" is off', $installer));
      }
      return ini_get('allow_url_fopen') && ini_get('allow_url_include');
    }
    return true;
  }
}
