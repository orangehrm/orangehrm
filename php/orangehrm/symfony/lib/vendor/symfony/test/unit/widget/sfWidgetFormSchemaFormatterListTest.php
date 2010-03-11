<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(2, new lime_output_color());

$f = new sfWidgetFormSchemaFormatterList(new sfWidgetFormSchema());

// ->formatRow()
$t->diag('->formatRow()');
$output = <<<EOF
<li>
  label
  <input /><br />help
</li>

EOF;
$t->is($f->formatRow('label', '<input />', array(), 'help', ''), $output, '->formatRow() formats a field in a row');

// ->formatErrorRow()
$t->diag('->formatErrorRow()');
$output = <<<EOF
<li>
  <ul class="error_list">
    <li>Global error</li>
    <li>id: required</li>
    <li>1 > sub_id: required</li>
  </ul>
</li>

EOF;
$t->is($f->formatErrorRow(array('Global error', 'id' => 'required', array('sub_id' => 'required'))), $output, '->formatErrorRow() formats an array of errors in a row');
