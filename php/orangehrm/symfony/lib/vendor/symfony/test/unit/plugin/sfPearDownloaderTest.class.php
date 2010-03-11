<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once 'PEAR/Downloader.php';

/**
 * sfPearDownloaderTest is a class to be able to test a PEAR channel without the HTTP layer.
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPearDownloaderTest.class.php 5250 2007-09-24 08:11:50Z fabien $
 */
class sfPearDownloaderTest extends sfPearDownloader
{
  /**
   * @see PEAR_REST::downloadHttp()
   */
  public function downloadHttp($url, &$ui, $save_dir = '.', $callback = null, $lastmodified = null, $accept = false, $channel = false)
  {
    try
    {
      $file = sfPluginTestHelper::convertUrlToFixture($url);
    }
    catch (sfException $e)
    {
      return PEAR::raiseError($e->getMessage());
    }

    if ($lastmodified === false || $lastmodified)
    {
      return array($file, 0, array());
    }

    return $file;
  }
}
