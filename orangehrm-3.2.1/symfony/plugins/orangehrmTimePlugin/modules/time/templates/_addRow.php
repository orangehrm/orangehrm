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
<?php echo javascript_include_tag(plugin_web_path('orangehrmTimePlugin', 'js/editTimesheet')); ?>
<table id="newRow">
    <tr class="<?php echo ($num & 1) ? 'even' : 'odd' ?>">
        <td id="">
            <?php echo $form['initialRows'][$num]['toDelete'] ?>
        </td>
        <td>
            <?php echo $form['initialRows'][$num]['projectName']->renderError() ?>
            <?php echo $form['initialRows'][$num]['projectName']->render(array("class" => "project", "size" => 25)); ?>
            <?php echo $form['initialRows'][$num]['projectId'] ?>
        </td>
        <td>
            <?php echo $form['initialRows'][$num]['projectActivityName']->renderError() ?>
            <?php echo $form['initialRows'][$num]['projectActivityName']->render(array("class" => "projectActivity")); ?>
            <?php echo $form['initialRows'][$num]['projectActivityId'] ?>
        </td>
        <?php for ($j = 0; $j < $noOfDays; $j++) { ?>
            <td class="center comments">
                <?php echo $form['initialRows'][$num][$j]->renderError() ?>
                <?php echo $form['initialRows'][$num][$j]->render(array("class" => "timeBox")) ?>
                <?php echo image_tag(theme_path('images/comment.png'), 'id=commentBtn_' . $j . '_' . $num . " class=commentIcon ") ?>
                <?php echo $form['initialRows'][$num]['TimesheetItemId' . $j] ?>
            </td>
        <?php } ?>
    </tr>
</table>
