<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 6/9/18
 * Time: 10:29 AM
 */
?>
<form id="frmPurgeEmployee" method="post" action="<?php echo url_for('maintenance/purgeEmployee'); ?>">

    <div class="box">
        <?php include_partial('global/flash_messages'); ?>

        <div class="head">
            <h1><?php echo __('Purge Employee Records'); ?></h1>
        </div>

        <div class="inner">
            <fieldset>
                <div class="input-field col s12 m12 l4">
                    <ol>
                        <?php echo $form->render(); ?>
                    </ol>
                </div>
            </fieldset>
            <div class="input-field col s12 m12 l4">
                <br>
                <input class="search_employee" type="button" value="Search">
            </div>
        </div>

    </div>


    <div class="box" id="selected_employee">
    </div>
</form>

<script>
    $(document).ready(function () {
        $(".search_employee").click(function () {
            var emp_id = $("#employee_empId").val()
            if (emp_id > 0) {
                var data = getEmployeeData(emp_id)
            } else {
                alert("Select employee")
            }
        });
    });

    function getEmployeeData(id) {
        $.ajax({
            method: "POST",
            data: {empployeeID: id},
            url: "<?php echo url_for('maintenance/employeeData'); ?>", success: function (result) {
                $("#selected_employee").html(result);
                console.log(id)
            }
        });
    }
</script>


