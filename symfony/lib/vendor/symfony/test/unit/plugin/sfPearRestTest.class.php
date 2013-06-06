<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfPearRestTest is a class to be able to test a PEAR channel without the HTTP layer.
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPearRestTest.class.php 5250 2007-09-24 08:11:50Z fabien $
 */
class sfPearRestTest extends sfPearRest
{
  /**
   * @see PEAR_REST::downloadHttp()
   */
  public function downloadHttp($url, $lastmodified = null, $accept = false)
  {
    try
    {
      $file = sfPluginTestHelper::convertUrlToFixture($url);
    }
    catch (sfException $e)
    {
      return PEAR::raiseError($e->getMessage());
    }

    $headers = array(
      'content-type' => preg_match('/\.xml$/', $file) ? 'text/xml' : 'text/plain',
    );

    return array(file_get_contents($file), 0, $headers);
  }

  // Disable caching for testing
  public function saveCache($url, $contents, $lastmodified, $nochange = false, $cacheid = null)
  {
    return false;
  }
}
