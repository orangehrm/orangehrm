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

// filters
$b->
  checkListCustomization('filters', array('filters' => array('title', 'body', 'online', 'category_id', 'created_at')))->
  checkResponseElement('div.sf_admin_filters label[for="filters_title"]', 'Title:')->
  checkResponseElement('div.sf_admin_filters input[name="filters[title]"][id="filters_title"]')->
  checkResponseElement('div.sf_admin_filters label[for="filters_body"]', 'Body:')->
  checkResponseElement('div.sf_admin_filters input[name="filters[body]"][id="filters_body"]')->
  checkResponseElement('div.sf_admin_filters label[for="filters_online"]', 'Online:')->
  checkResponseElement('div.sf_admin_filters select[name="filters[online]"][id="filters_online"] option', 3)->
  checkResponseElement('div.sf_admin_filters label[for="filters_category_id"]', 'Category:')->
  checkResponseElement('div.sf_admin_filters select[name="filters[category_id]"][id="filters_category_id"] option', 3)->
  checkResponseElement('div.sf_admin_filters label[for="filters_created_at"]', 'Created at:')->
  checkResponseElement('div.sf_admin_filters input[name="filters[created_at][from]"][id="filters_created_at_from"]')->
  checkResponseElement('div.sf_admin_filters input[name="filters[created_at][to]"][id="filters_created_at_to"]')
;

$b->
  checkListCustomization('filters', array('filters' => array('title'), 'fields' => array('title' => array('filter_is_empty' => true))))->
  checkResponseElement('div.sf_admin_filters label[for="filters_title_is_empty"]')
;
