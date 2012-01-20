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
 * @version    SVN: $Id: sfProjectEnableTask.class.php 23922 2009-11-14 14:58:38Z fabien $
 */
class sfProjectEnableTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('env', sfCommandArgument::REQUIRED, 'The environment name'),
      new sfCommandArgument('app', sfCommandArgument::OPTIONAL | sfCommandArgument::IS_ARRAY, 'The application name'),
    ));

    $this->namespace = 'project';
    $this->name = 'enable';
    $this->briefDescription = 'Enables an application in a given environment';

    $this->detailedDescription = <<<EOF
The [project:enable|INFO] task enables a specific environment:

  [./symfony project:enable frontend prod|INFO]

You can also specify individual applications to be enabled in that
environment:

  [./symfony project:enable prod frontend backend|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    if (1 == count($arguments['app']) && !file_exists(sfConfig::get('sf_apps_dir').'/'.$arguments['app'][0]))
    {
      // support previous task signature
      $applications = array($arguments['env']);
      $env = $arguments['app'][0];
    }
    else
    {
      $applications = count($arguments['app']) ? $arguments['app'] : sfFinder::type('dir')->relative()->maxdepth(0)->in(sfConfig::get('sf_apps_dir'));
      $env = $arguments['env'];
    }

    foreach ($applications as $app)
    {
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
        $clearCache->setConfiguration($this->configuration);
        $clearCache->run(array(), array('--app='.$app, '--env='.$env));

        $this->logSection('enable', sprintf('%s [%s] has been ENABLED', $app, $env));
      }
    }
  }
}
