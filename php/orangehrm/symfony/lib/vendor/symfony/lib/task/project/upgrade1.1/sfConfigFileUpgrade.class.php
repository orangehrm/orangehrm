<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Detects obsoletes configuration files.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfConfigFileUpgrade.class.php 7434 2008-02-09 15:32:51Z fabien $
 */
class sfConfigFileUpgrade extends sfUpgrade
{
  public function upgrade()
  {
    foreach (array('logging.yml', 'i18n.yml') as $name)
    {
      $finder = $this->getFinder('file')->name($name);
      foreach ($finder->in($this->getProjectConfigDirectories()) as $file)
      {
        $this->logSection('config', sprintf('The following file is not used anymore. Please remove it.', $file));
        $this->log('   '.$file);
      }
    }
  }
}
