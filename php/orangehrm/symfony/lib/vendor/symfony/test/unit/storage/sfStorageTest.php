<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(0, new lime_output_color());

class myStorage extends sfStorage
{
  public function read($key) {}
  public function remove($key) {}
  public function shutdown() {}
  public function write($key, $data) {}
  public function regenerate($destroy = false) {}
}

class fakeStorage
{
}
