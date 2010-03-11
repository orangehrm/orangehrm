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

$b = new sfTestBrowser();

// test action method inheritance
$b->
  get('/inheritance/list?filter=1')->
  checkResponseElement('table.sf_admin_list tfoot th', '/1 result/')
;

$b->
  get('/inheritance/list?sort=1')->
  checkResponseElement('table.sf_admin_list tbody tr.sf_admin_row_0 a', '/2/')->
  checkResponseElement('table.sf_admin_list tbody tr.sf_admin_row_1 a', '/1/')
;
