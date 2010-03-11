<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once(dirname(__FILE__).'/../../../lib/helper/TagHelper.php');
require_once(dirname(__FILE__).'/../../../lib/helper/JavascriptBaseHelper.php');

$t = new lime_test(7, new lime_output_color());

// boolean_for_javascript()
$t->diag('boolean_for_javascript()');
$t->is(boolean_for_javascript(true), 'true', 'boolean_for_javascript() makes a javascript representation of the boolean if the param is boolean');
$t->is(boolean_for_javascript(false), 'false', 'boolean_for_javascript() makes a javascript representation of the boolean if the param is boolean');
$t->is(boolean_for_javascript(1==0), 'false', 'boolean_for_javascript() makes a javascript representation of the boolean if the param is boolean');
$t->is(boolean_for_javascript('dummy'), 'dummy', 'boolean_for_javascript() makes a javascript representation of the boolean if the param is boolean');

//options_for_javascript()
$t->diag('options_for_javascript()');
$t->is(options_for_javascript(array("'a'" => "'b'", "'c'" => false)), "{'a':'b', 'c':false}", 'options_for_javascript() makes a javascript representation of the passed array');
$t->is(options_for_javascript(array("'a'" => array ("'b'" => "'c'"))), "{'a':{'b':'c'}}", 'options_for_javascript() works with nested arrays');

//javascript_tag()
$t->diag('javascript_tag()');
$expect = <<<EOT
<script type="text/javascript">
//<![CDATA[
alert("foo");
//]]>
</script>
EOT;
$t->is(javascript_tag('alert("foo");'), $expect, 'javascript_tag() takes the content as string parameter');
