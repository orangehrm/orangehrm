<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/crudBrowser.class.php');

class RestBrowser extends CrudBrowser
{
  protected
    $urlPrefix = 'articles';

  public function setup($options)
  {
    $this->projectDir = dirname(__FILE__).'/../fixtures';
    $this->cleanup();

    chdir($this->projectDir);
    $task = new sfPropelGenerateModuleForRouteTask(new sfEventDispatcher(), new sfFormatter());
    $options[] = 'env=test';
    $options[] = '--non-verbose-templates';
    $task->run(array('crud', 'articles'), $options);

    require_once($this->projectDir.'/config/ProjectConfiguration.class.php');
    sfContext::createInstance(ProjectConfiguration::getApplicationConfiguration('crud', 'test', true, $this->projectDir));

    $options['with-show'] = true;

    return $options;
  }
}
