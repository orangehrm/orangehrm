<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Notifies sfViewCacheManager changes.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class sfViewCacheManagerUpgrade extends sfUpgrade
{
  public function upgrade()
  {
    $filtersFinder = $this->getFinder('file')->name('*.php');
    foreach ($filtersFinder->in($this->getProjectActionDirectories()) as $file)
    {
      $content = file_get_contents($file);
      if (preg_match("#\->(removePattern|clearGlob)\(#s", $content, $matches))
      {
        $this->logSection('cache', sprintf('Action "%s" calls the "%s()" method but the syntax has changed. Please, read the documentation and upgrade accordingly.', $file, $matches[1]), 1000);
      }
    }
  }
}
