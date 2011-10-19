<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>

<div class="outerbox">
    <div class="mainHeading"><h2 id="dateTestHeading"><?php echo __('Date Test'); ?></h2></div>
    <form name="dateTest" id="dateTest" action="<?php echo url_for("admin/dateTest") ?>" method="post">
        <?php echo $form['_csrf_token']; ?>
        <br class="clear"/>
        <?php echo $form['fromDate']->renderLabel(__('From')); ?>
        <?php echo $form['fromDate']->render(array("class" => "frmDate")); ?>
        <?php echo $form['fromDate']->renderError(); ?>
        <br class="clear"/>
        <?php echo $form['toDate']->renderLabel(__('To')); ?>
        <?php echo $form['toDate']->render(array("class" => "toDate")); ?>
        <?php echo $form['toDate']->renderError(); ?>
        <br class="clear"/>
        <div class="formbuttons">
            <input type="button" class="savebutton" name="btnSave" id="btnSave"
                   value="<?php echo __("Save"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
        </div>
    </form>
</div>

<script type="text/javascript">
    //<![CDATA[
    $(document).ready(function() {
        var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
        var lang_dateError = '<?php echo __("To date should be after the From date") ?>';
        var lang_invalidDate = '<?php echo __("Please enter a valid date in %format% format", array('%format%' => get_datepicker_date_format($sf_user->getDateFormat()))); ?>';

        var fromdate = "";
        var todate = "";
        
        $('#btnSave').click(function() {
            fromdate = $('#from_date').val();
            todate = $('#to_date').val();
            if($('#from_date').val() == datepickerDateFormat){
                $('#from_date').val("");
            }
            if($('#to_date').val() == datepickerDateFormat){
                $('#to_date').val("");
            }
            $('#dateTest').submit()
        });

        var validator = $("#dateTest").validate({

            rules: {
                'dateTest[fromDate]' : {
                    required: true,
                    valid_date: function() {
                        return {
                            format:datepickerDateFormat
                        }
                    }
                },
                'dateTest[toDate]' : {
                    required: true,
                    valid_date: function() {
                        return {
                            format:datepickerDateFormat
                        }
                    },
                    date_range: function() {
                        return {
                            format:datepickerDateFormat,
                            fromDate:fromdate
                        }
                    }
                }
            },
            messages: {
                'dateTest[fromDate]' : {
                    required: lang_invalidDate,
                    valid_date: lang_invalidDate
                },
                'dateTest[toDate]' : {
                    required: lang_invalidDate,
                    valid_date: lang_invalidDate ,
                    date_range: lang_dateError
                }

            },
            errorPlacement: function(error, element) {
                error.appendTo(element.prev('label'));
                //                error.appendTo(element.next().next().next('div.errorDiv'));
            }

        });



    });
    //]]>
</script>
