<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Disables an application in a given environment.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfProjectDisableTask.class.php 23922 2009-11-14 14:58:38Z fabien $
 */
class sfProjectDisableTask extends sfBaseTask
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
    $this->name = 'disable';
    $this->briefDescription = 'Disables an application in a given environment';

    $this->detailedDescription = <<<EOF
The [project:disable|INFO] task disables an environment:

  [./symfony project:disable prod|INFO]

You can also specify individual applications to be disabled in that
environment:

  [./symfony project:disable prod frontend backend|INFO]
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
      if (file_exists($lockFile))
      {
        $this->logSection('enable', sprintf('%s [%s] is currently DISABLED', $app, $env));
      }
      else
      {
        $this->getFilesystem()->touch($lockFile);

        $this->logSection('enable', sprintf('%s [%s] has been DISABLED', $app, $env));
      }
    }
  }
}
