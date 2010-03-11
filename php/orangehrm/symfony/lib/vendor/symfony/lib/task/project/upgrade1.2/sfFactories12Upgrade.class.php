<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Upgrades factories.yml configuration file.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfFactories12Upgrade.class.php 10628 2008-08-03 15:03:08Z fabien $
 */
class sfFactories12Upgrade extends sfUpgrade
{
  public function upgrade()
  {
    $phpFinder = $this->getFinder('file')->name('factories.yml');
    foreach ($phpFinder->in($this->getProjectConfigDirectories()) as $file)
    {
      $content = file_get_contents($file);

      if (!preg_match('/send_http_headers/', $content))
      {
        $tmp = <<<EOF
  response:
    class: sfWebResponse
    param:
      send_http_headers: false

EOF;

        $content = str_replace('test:', 'test:'."\n".$tmp, $content);

        $this->logSection('factories.yml', sprintf('Migrating %s', $file));
        file_put_contents($file, $content);
      }
    }
  }
}
