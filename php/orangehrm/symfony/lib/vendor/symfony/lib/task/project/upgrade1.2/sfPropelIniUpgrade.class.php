<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Upgrade propel.ini.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelIniUpgrade.class.php 11517 2008-09-13 20:23:56Z fabien $
 */
class sfPropelIniUpgrade extends sfUpgrade
{
  public function upgrade()
  {
    $file = sfConfig::get('sf_config_dir').'/propel.ini';
    if(is_readable($file))
    {
      $content = file_get_contents($file);
      $content = str_replace('plugins.sfPropelPlugin.lib.propel.builder.', 'plugins.sfPropelPlugin.lib.builder.', $content, $count);

      if ($count)
      {
        $this->logSection('propel', sprintf('Migrating %s', $file));
        file_put_contents($file, $content);
      }
    }
  }
}
