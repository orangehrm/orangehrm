<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Upgrades settings.yml configuration file.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfSettingsUpgrade.class.php 9793 2008-06-23 09:48:20Z fabien $
 */
class sfSettingsUpgrade extends sfUpgrade
{
  public function upgrade()
  {
    $phpFinder = $this->getFinder('file')->name('settings.yml');
    foreach ($phpFinder->in($this->getProjectConfigDirectories()) as $file)
    {
      $content = file_get_contents($file);
      $content = preg_replace(
        '#error_reporting\:(\s+)'.preg_quote('<?php echo (E_ALL | E_STRICT & ~E_NOTICE)."\n" ?>', '#').'#',
        'error_reporting:$1<?php echo ((E_ALL | E_STRICT) ^ E_NOTICE)."\n" ?>',
        $content, -1, $count1
      );
      $content = preg_replace(
        '#error_reporting\:(\s+)4095#',
        'error_reporting:$1<?php echo (E_ALL | E_STRICT)."\n" ?>',
        $content, -1, $count2
      );
      $content = preg_replace(
        '#error_reporting\:(\s+)2047#',
        'error_reporting:$1<?php echo ((E_ALL | E_STRICT) ^ E_NOTICE)."\n" ?>',
        $content, -1, $count3
      );

      if ($count1 || $count2 || $count3)
      {
        $this->logSection('settings.yml', sprintf('Migrating %s', $file));
        file_put_contents($file, $content);
      }
    }
  }
}
