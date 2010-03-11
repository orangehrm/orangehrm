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

$b->
  checkListCustomization('add batch action', array('batch_actions' => array('_deleteSelected' => null, 'custom' => array('name' => 'my button', 'action' => 'myAction'))))->
  checkResponseElement('table.sf_admin_list tr.sf_admin_row_0 td input[class="sf_admin_batch_checkbox"][type="checkbox"]', true)->
  checkResponseElement('body div[id="sf_admin_batch_action_choice"] select[name="sf_admin_batch_action"]', true)->
  checkResponseElement('body div[id="sf_admin_batch_action_choice"] select[name="sf_admin_batch_action"] option[value="deleteSelected"]', "Delete Selected")->
  checkResponseElement('body div[id="sf_admin_batch_action_choice"] select[name="sf_admin_batch_action"] option[value="custom"]', "my button")->
  setField('sf_admin_batch_selection[]', array(1, 3, 5))->
  setField('sf_admin_batch_action', 'custom')->
  click('Ok')->
  isStatusCode(200)->
  responseContains('Selected 1, 3, 5')
;
