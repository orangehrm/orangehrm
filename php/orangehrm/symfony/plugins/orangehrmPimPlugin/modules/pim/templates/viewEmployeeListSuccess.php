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

<?php
use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css');
use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css');
use_stylesheet('../orangehrmPimPlugin/css/viewEmployeeListSuccess');
use_javascript('../../../scripts/jquery/ui/ui.core.js');
use_javascript('../../../scripts/jquery/jquery.autocomplete.js');
?>


<?php if ($form->hasErrors()): ?>
<span class="error">
<?php
echo $form->renderGlobalErrors();

foreach($form->getWidgetSchema()->getPositions() as $widgetName) {
  echo $form[$widgetName]->renderError();
}
?>
</span>
<?php endif; ?>
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
    <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
</div>

<div class="outerbox">

<div class="mainHeading">
	<h2><?php echo __("Employee Information") ?></h2>
</div>

<?php if ($form->hasErrors() || $sf_user->hasFlash('success') || $sf_user->hasFlash('error')): ?>
<div class="messagebar">
    <?php include_partial('global/form_errors', array('form' => $form));?>
    <?php include_partial('global/flash_messages', array('sf_user' => $sf_user));?>
</div>
<?php endif; ?>
<?php if($filterApply) {?>
<div class="messagebar">
   <?php echo __("This result set has filters applied to it. To clear the filters use the Reset button"); ?>
</div>
<?php }?>
<div class="searchbox">
<form id="search_form" method="post" action="<?php echo url_for('@employee_list'); ?>">
    <div id="formcontent">
    <?php echo $form['_csrf_token'];
	  echo $form['employee_name']->renderLabel(__("Employee Name"));
          echo $form['employee_name']->render();

          echo $form['id']->renderLabel(__("Id"));
          echo $form['id']->render();

          echo $form['employee_status']->renderLabel(__("Employee Status"));
          echo $form['employee_status']->render();

    ?>
    <br class="clear"/>
    <?php
          echo $form['supervisor_name']->renderLabel(__("Supervisor Name"));
          echo $form['supervisor_name']->render();

          echo $form['job_title']->renderLabel(__("Job Title"));
          echo $form['job_title']->render();

          echo $form['sub_unit']->renderLabel(__("Sub Unit"));
          echo $form['sub_unit']->render();

    ?>
    </div>
    <div class="actionbar">
    <div class="actionbuttons">
        <input
            type="button" class="plainbtn" id="searchBtn"
            onmouseover="this.className='plainbtn plainbtnhov'"
            onmouseout="this.className='plainbtn'" value="<?php echo __("Search")?>" name="_search" />
        <input
            type="button" class="plainbtn"
            onmouseover="this.className='plainbtn plainbtnhov'" id="resetBtn"
            onmouseout="this.className='plainbtn'" value="<?php echo __("Reset")?>" name="_reset" />

    </div>
    <br class="clear" />
    </div>
    <br class="clear" />
</form>
</div>
</div> <!-- outerbox -->

<div class="outerbox">
<form method="post" action="<?php echo url_for('pim/deleteEmployees');?>" id="frmDelete" >

<div class="actionbar">
<div class="actionbuttons">
<?php if ($sf_user->hasCredential(Auth::ADMIN_ROLE)) { ?>    
    <input type="button" class="plainbtn" id="addBtn"
    	onmouseover="this.className='plainbtn plainbtnhov'"
	    onmouseout="this.className='plainbtn'" value="<?php echo __("Add")?>" />
<?php } ?>
<?php if ($sf_user->hasCredential(Auth::ADMIN_ROLE) && (count($employee_list) > 0)) { ?>
    <input type="submit" class="plainbtn"
        onmouseover="this.className='plainbtn plainbtnhov'"
        onmouseout="this.className='plainbtn'" value="<?php echo __("Delete")?>" />
<?php } ?>
</div>
<div class="noresultsbar"><?php //echo (empty($emplist)) ? $norecorddisplay : '';?></div>

<?php if ($pager->haveToPaginate()): ?>
<div class="pagingbar">
    <?php include_partial('global/paging_links', array('pager' => $pager, 'url'=>'@employee_list'));?>
</div>

<?php endif; ?>

<br class="clear" />
</div>
<br class="clear" />
<table cellspacing="0" cellpadding="0" class="data-table" id="emp_list">
	<thead>
		<tr>
			<td width="50">
               <?php if ($sf_user->hasCredential(Auth::ADMIN_ROLE) && (count($employee_list) > 0)) { ?>
               <input type="checkbox" id="allCheck" class="checkbox" style="margin-left:1px" />
               <?php } ?>
			</td>
			<td scope="col"><?php echo $sorter->sortLink('employeeId', __('Id'), '@employee_list', ESC_RAW); ?></td>
			<td scope="col"><?php echo $sorter->sortLink('firstName', __('First Name'), '@employee_list', ESC_RAW); ?></td>
                        <td scope="col"><?php echo $sorter->sortLink('lastName', __('Last Name'), '@employee_list', ESC_RAW); ?></td>
			<td scope="col"><?php echo $sorter->sortLink('jobTitle', __('Job Title'), '@employee_list', ESC_RAW); ?></td>
			<td scope="col"><?php echo $sorter->sortLink('employeeStatus', __('Employment Status'), '@employee_list', ESC_RAW); ?></td>
			<td scope="col"><?php echo $sorter->sortLink('subDivision', __('Sub Unit'), '@employee_list', ESC_RAW); ?></td>
			<?php
    			/* Show supervisor only for admin users, not for supervisors */
    			if ($sf_user->hasCredential(Auth::ADMIN_ROLE)) {
            ?>
			<td scope="col"><?php echo $sorter->sortLink('supervisor', __('Supervisor'), '@employee_list', ESC_RAW); ?></td>
			<?php } ?>
		</tr>
	</thead>

	<tbody>
		<?php
					    $row = 0;
						foreach ($employee_list as $employee):
							$cssClass = ($row %2) ? 'even' : 'odd';
							$row = $row + 1;
						?>

		<tr class="<?php echo $cssClass;?>">
			<?php if ($sf_user->hasCredential(Auth::ADMIN_ROLE) ){ ?>
			<td><input type="checkbox" class="checkbox" name="ids[]"
				value="<?php echo $employee->getEmpNumber() ?>" /></td>
			<?php } else { ?>
			<td></td>
			<?php } ?>

			<td>
			<?php
			$empId = $employee->getEmployeeId();

			if (empty($empId)) {
			    $empId = str_pad($employee->getEmpNumber(), IDGeneratorService::MIN_LENGTH, "0", STR_PAD_LEFT);
			}

			echo link_to($empId, "pim/viewPersonalDetails?empNumber=" . $employee->getEmpNumber());
			?>
			</td>
			<td>
			<?php
			    // Link to orangehrm page
/*			    $params = array('menu_no_top' => 'hr',
			                    'id' => format_emp_number($employee->getEmpNumber()),
			                    'capturemode' => 'updatemode',
			                    'reqcode' => 'EMP');*/
/*
			    echo link_to($employee->getFullName(), public_path('../../index.php'),
			                 array('query_string'=> http_build_query($params)) );
*/
			    echo link_to($employee->getFirstName(), "pim/viewPersonalDetails?empNumber=" . $employee->getEmpNumber());

			?>
			</td>
                        <td>
                            <?php echo link_to($employee->getLastName(), "pim/viewPersonalDetails?empNumber=" . $employee->getEmpNumber());?>
                        </td>
			<td><?php echo $employee->getJobTitle()->getName(); ?></td>
			<td><?php echo $employee->getEmployeeStatus()->getName() ?></td>
			<td><?php echo $employee->getSubDivision()->getTitle() ?></td>

			<?php /* Show supervisor only for admin users, not for supervisors */
			if ($sf_user->hasCredential(Auth::ADMIN_ROLE)): ?>
				<td><?php echo $employee->getSupervisorNames() ?></td>
			<?php endif; ?>
		</tr>
		<?php endforeach; ?>

	</tbody>
</table>
</form>
</div>

<script type="text/javascript">

    $(document).ready(function() {

        var employees = <?php echo str_replace('&#039;',"'",$form->getEmployeeListAsJson())?> ;
        var supervisors = <?php echo str_replace('&#039;',"'",$form->getSupervisorListAsJson())?> ;
        
	//Auto complete
        $("#empsearch_employee_name").autocomplete(employees, {
          formatItem: function(item) {
            return item.name;
          }
          ,matchContains:true
        }).result(function(event, item) {
        }
        );

        $("#empsearch_supervisor_name").autocomplete(supervisors, {
          formatItem: function(item) {
            return item.name;
          }
          ,matchContains:true
        }).result(function(event, item) {
        }
        );

        $('#allCheck').click(function() {
            var check = $(this).attr('checked');
            $('input[type=checkbox].checkbox').attr('checked', check);
        });

        $('#emp_list td').click(function() {
            
            if(!$(this).find("input").is('input:checkbox')) { // check if check box is clicked
                 href = $(this).parent().find("a").attr("href");
            }

            if(href) {
                window.location = href;
            }
        });

	$('#searchBtn').click(function() {
            $('#search_form').submit();
	});
       
	$('#resetBtn').click(function() {
            $("#empsearch_employee_name").val('');
            $("#empsearch_supervisor_name").val('');
            $("#empsearch_id").val('');
            $("#empsearch_job_title").val('0');
            $("#empsearch_employee_status").val('0');
            $("#empsearch_sub_unit").val('0');
            $('#search_form').submit();
	});

        $('#addBtn').click(function() {
            location.href = "<?php echo url_for('pim/addEmployee') ?>";
        });

	$('#frmDelete').submit(function() {

            var checked = $('#frmDelete input:checked').length;
            
            if (checked > 0) {
                return true;
            } else {
                $("#messagebar").attr('class', "messageBalloon_notice");
                $("#messagebar").text('<?php echo __("Select At Least One Employee To Delete"); ?>');
                return false;
            }
	});


        $('#emp_list tbody tr').hover(function() {  // highlight on mouse over
            colorbg = $(this).css('backgroundColor');
            $(this).removeClass();
            $(this).addClass("hoverOverEmp");
        });

        $('#emp_list tbody tr').mouseout(function() { // redraw table raws with alternate colors
           var i=0;          
           $('#emp_list tbody tr').each(function() {
               (i==0)?$(this).addClass('odd'):$(this).addClass('even');
                i=1-i;
            });
        });

    });

    

</script>