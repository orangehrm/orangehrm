<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
?>
<?php echo javascript_include_tag('../orangehrmTimePlugin/js/editTimesheet'); ?>
<table  class = "data-table" cellpadding ="0" border="0" cellspacing="0">
	<tr>
	    <td><?php echo $form['initialRows'][$num]['toDelete'] ?></td>
                <?php echo $form['initialRows'][$num]['projectId'] ?><td>&nbsp;<?php echo $form['initialRows'][$num]['projectName']->renderError() ?><?php echo $form['initialRows'][$num]['projectName'] ?></td>
		<?php echo $form['initialRows'][$num]['projectActivityId'] ?><td>&nbsp;<?php echo $form['initialRows'][$num]['projectActivityName']->renderError() ?><?php echo $form['initialRows'][$num]['projectActivityName'] ?></td>
		<?php for ($j = 0; $j < $noOfDays; $j++) { ?>
			<?php echo $form['initialRows'][$num]['TimesheetItemId'.$j] ?><td style="text-align:center"><?php echo $form['initialRows'][$num][$j]->renderError() ?><div style="float: left; padding-left: 20px"><?php echo $form['initialRows'][$num][$j] ?></div><div id="img" style="float: left; padding-left: 2px"><?php echo image_tag('callout.png', 'id=commentBtn_'.$j.'_' . $num . " class=commentIcon") ?></div></td>
		<?php } ?>
	</tr>
</table>


