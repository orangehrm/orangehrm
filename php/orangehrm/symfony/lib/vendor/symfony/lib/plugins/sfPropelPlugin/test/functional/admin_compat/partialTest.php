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

// partial in a tabular list
// we add a filters parameter because the action class won't be reloaded after the first time!
// and we need the filter line definition for the last test!
$b->
  checkListCustomization('partial support in tabular list', array('display' => array('_body'), 'filters' => array('title')))->
  checkResponseElement('body table tbody tr[class="sf_admin_row_0"] td', '/\s*before bar body after\s*/')->
  checkResponseElement('body table tbody tr[class="sf_admin_row_1"] td', '/\s*before bar bar body after\s*/')
;

// partial in a stacked list
$b->
  checkListCustomization('partial support in tabular list', array('layout' => 'stacked', 'params' => "%%_body%%"))->
  checkResponseElement('body table tbody tr[class="sf_admin_row_0"] td', '/\s*before bar body after\s*/')->
  checkResponseElement('body table tbody tr[class="sf_admin_row_1"] td', '/\s*before bar bar body after\s*/')
;

// partial in edit
$b->
  checkEditCustomization('partial support in edit', array('display' => array('_body')))->
  checkResponseElement('body form#sf_admin_edit_form textarea[name="article[body]"][id="article_body"]', '/\s*before bar body after\s*/')
;

// partial for a filter
$b->
  checkListCustomization('partial support for a filter', array('filters' => array('_body')))->
  checkResponseElement('body div.sf_admin_filters form input[name="body_filter"][value="before after"]')
;
