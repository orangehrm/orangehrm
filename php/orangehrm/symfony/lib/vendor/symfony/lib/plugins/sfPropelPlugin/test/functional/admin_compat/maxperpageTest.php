<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'backend_compat';
$fixtures = 'fixtures/fixtures.yml';
if (!include(dirname(__FILE__).'/../../bootstrap/functional.php'))
{
  return;
}

include(dirname(__FILE__).'/backendTestBrowser.class.php');

$b = new backendTestBrowser();

// max per page
$b->
  checkListCustomization('max per page customization', array('max_per_page' => 1))->
  checkResponseElement('body table tfoot tr th a[href*="/article/list/page/2"]', true)->
  checkResponseElement('body table tbody tr', 1)
;
