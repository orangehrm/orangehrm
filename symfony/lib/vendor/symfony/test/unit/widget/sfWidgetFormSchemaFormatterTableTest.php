<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(2);

$f = new sfWidgetFormSchemaFormatterTable(new sfWidgetFormSchema());

// ->formatRow()
$t->diag('->formatRow()');
$output = <<<EOF
<tr>
  <th>label</th>
  <td><input /><br />help</td>
</tr>

EOF;
$t->is($f->formatRow('label', '<input />', array(), 'help', ''), fix_linebreaks($output), '->formatRow() formats a field in a row');

// ->formatErrorRow()
$t->diag('->formatErrorRow()');
$output = <<<EOF
<tr><td colspan="2">
  <ul class="error_list">
    <li>Global error</li>
    <li>id: required</li>
    <li>1 > sub_id: required</li>
  </ul>
</td></tr>

EOF;
$t->is($f->formatErrorRow(array('Global error', 'id' => 'required', array('sub_id' => 'required'))), fix_linebreaks($output), '->formatErrorRow() formats an array of errors in a row');
