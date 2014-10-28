<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Clears all non production environment controllers.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfProjectClearControllersTask.class.php 23922 2009-11-14 14:58:38Z fabien $
 */
class sfProjectClearControllersTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->namespace = 'project';
    $this->name = 'clear-controllers';
    $this->briefDescription = 'Clears all non production environment controllers';

    $this->detailedDescription = <<<EOF
The [project:clear-controllers|INFO] task clears all non production environment
controllers:

  [./symfony project:clear-controllers|INFO]

You can use this task on a production server to remove all front
controller scripts except the production ones.

If you have two applications named [frontend|COMMENT] and [backend|COMMENT],
you have four default controller scripts in [web/|COMMENT]:

  [index.php
  frontend_dev.php
  backend.php
  backend_dev.php|INFO]

After executing the [project:clear-controllers|COMMENT] task, two front
controller scripts are left in [web/|COMMENT]:

  [index.php
  backend.php|INFO]

Those two controllers are safe because debug mode and the web debug
toolbar are disabled.
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $finder = sfFinder::type('file')->maxdepth(1)->name('*.php');
    foreach ($finder->in(sfConfig::get('sf_web_dir')) as $controller)
    {
      $content = file_get_contents($controller);

      if (preg_match('/ProjectConfiguration::getApplicationConfiguration\(\'(.*?)\', \'(.*?)\'/', $content, $match))
      {
        // Remove file if it has found an application and the environment is not production
        if ($match[2] != 'prod')
        {
          $this->getFilesystem()->remove($controller);
        }
      }
    }
  }
}
