<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once($_test_dir.'/unit/sfContextMock.class.php');
require_once(dirname(__FILE__).'/TestObject.php');

require_once(dirname(__FILE__).'/../../../lib/helper/HelperHelper.php');
require_once(dirname(__FILE__).'/../../../lib/helper/TagHelper.php');
require_once(dirname(__FILE__).'/../../../lib/helper/FormHelper.php');
require_once(dirname(__FILE__).'/../../../lib/helper/ObjectHelper.php');

$t = new lime_test(10, new lime_output_color());

// object_textarea_tag()
$t->diag('object_textarea_tag()');
$obj1 = new TestObject();

$t->is(object_textarea_tag($obj1, 'getValue'),
                   '<textarea name="value" id="value">value</textarea>');
$t->is(object_textarea_tag($obj1, 'getValue', 'size=60x10'),
                   '<textarea name="value" id="value" rows="10" cols="60">value</textarea>');

// objects_for_select()
$t->diag('objects_for_select()');
$obj1 = new TestObject();
$obj2 = new TestObject();
$obj2->setText('text2');
$obj2->setValue('value2');

$actual = objects_for_select(Array($obj1, $obj2), 'getValue', 'getText', 'value');
$expected = "<option value=\"value\" selected=\"selected\">text</option>\n<option value=\"value2\">text2</option>\n";
$t->is($expected, $actual);

$actual = objects_for_select(Array($obj1, $obj2), 'getValue');
$expected = "<option value=\"value\">value</option>\n<option value=\"value2\">value2</option>\n";
$t->is($expected, $actual);

try
{
  $actual = objects_for_select(Array($obj1, $obj2), 'getNonExistantMethod');
  $t->is($expected, $actual);

  $t->fail();
}
catch (sfViewException $e)
{
  $t->pass();
}

try
{
  $actual = objects_for_select(Array($obj1, $obj2), 'getValue', 'getNonExistantMethod');
  $t->is($expected, $actual);

  $t->fail();
}
catch (sfViewException $e)
{
  $t->pass();
}

// object_input_hidden_tag()
$t->diag('object_input_hidden_tag()');
$obj1 = new TestObject();

$t->is(object_input_hidden_tag($obj1, 'getValue'),
                   '<input type="hidden" name="value" id="value" value="value" />');

// object_input_tag()
$t->diag('object_input_tag()');
$obj1 = new TestObject();

$t->is(object_input_tag($obj1, 'getValue'),
                   '<input type="text" name="value" id="value" value="value" />');

// object_checkbox_tag()
$t->diag('object_checkbox_tag()');
$obj1 = new TestObject();

$t->is(object_checkbox_tag($obj1, 'getBooleanFalse'),
                   '<input type="checkbox" name="boolean_false" id="boolean_false" value="1" />');

$t->is(object_checkbox_tag($obj1, 'getBooleanTrue'),
                   '<input type="checkbox" name="boolean_true" id="boolean_true" value="1" checked="checked" />');
