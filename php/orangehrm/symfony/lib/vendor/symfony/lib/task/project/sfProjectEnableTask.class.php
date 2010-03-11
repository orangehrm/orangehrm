<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Enables an application in a given environment.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfProjectEnableTask.class.php 15156 2009-01-31 21:44:54Z FabianLange $
 */
class sfProjectEnableTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
      new sfCommandArgument('env', sfCommandArgument::REQUIRED, 'The environment name'),
    ));

    $this->aliases = array('enable');
    $this->namespace = 'project';
    $this->name = 'enable';
    $this->briefDescription = 'Enables an application in a given environment';

    $this->detailedDescription = <<<EOF
The [project:enable|INFO] task enables an application for a specific environment:

  [./symfony project:enable frontend prod|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $app = $arguments['application'];
    $env = $arguments['env'];

    $lockFile = sfConfig::get('sf_data_dir').'/'.$app.'_'.$env.'.lck';
    if (!file_exists($lockFile))
    {
      $this->logSection('enable', sprintf('%s [%s] is currently ENABLED', $app, $env));
    }
    else
    {
      $this->getFilesystem()->remove($lockFile);

      $clearCache = new sfCacheClearTask($this->dispatcher, $this->formatter);
      $clearCache->setCommandApplication($this->commandApplication);
      $clearCache->run(array(), array('--app='.$app, '--env='.$env));

      $this->logSection('enable', sprintf('%s [%s] has been ENABLED', $app, $env));
    }
  }
}
