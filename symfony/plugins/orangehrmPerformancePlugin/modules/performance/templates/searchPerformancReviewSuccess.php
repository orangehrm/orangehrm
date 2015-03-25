<?php use_stylesheets_for_form($form); ?>
<?php if ($form->hasErrors()): ?>
    <div class="messagebar">
        <?php include_partial('global/form_errors', array('form' => $form)); ?>
    </div>
<?php endif; ?>
<div class="box searchForm toggableForm" id="leave-list-search">
    <div class="head">
        <h1><?php echo __('Search Performance Reviews') ?></h1>
    </div>
    <div class="inner">
        <form id="performanceReview360SearchForm" name="performanceReview360SearchForm" method="post" action="">
            <fieldset>                
                <ol>
                    <?php echo $form->render(); ?>
                </ol>                            
                <p>
                    <?php
                    $searchActionButtons = $form->getSearchActionButtons();
                    foreach ($searchActionButtons as $id => $button) {
                        echo $button->render($id), "\n";
                    }
                    ?>                    
                    <?php include_component('core', 'ohrmPluginPannel', array('location' => 'listing_layout_navigation_bar_1')); ?>
                    <input type="hidden" name="pageNo" id="pageNo" value="" />
                    <input type="hidden" name="hdnAction" id="hdnAction" value="search" />                    
                </p>                
            </fieldset>

        </form>

    </div> 
    <a href="#" class="toggle tiptip" title="<?php echo __(CommonMessages::TOGGABLE_DEFAULT_MESSAGE); ?>">&gt;</a>
</div>

<!--new end-->

<?php include_component('core', 'ohrmList'); ?>
<?php include_partial('global/delete_confirmation'); ?>
<script>
    $(document).ready(function () {

        var resetUrl = '<?php echo url_for('performance/searchPerformancReview' . '?reset=1'); ?>';
        var employees = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?>;
        var employeeList = eval(employees);
        var typeHint = '<?php echo __("Type for hints") . "..."; ?>';

        $('#btnDelete').attr('disabled', 'disabled');

        $("#ohrmList_chkSelectAll").change(function () {
            if ($(":checkbox").length == 1) {
                $('#btnDelete').attr('disabled', 'disabled');
            }
            else {
                if ($("#ohrmList_chkSelectAll").is(':checked')) {
                    $('#btnDelete').removeAttr('disabled');
                } else {
                    $('#btnDelete').attr('disabled', 'disabled');
                }
            }
        });

        $(':checkbox[name*="chkSelectRow[]"]').click(function () {
            if ($(':checkbox[name*="chkSelectRow[]"]').is(':checked')) {
                $('#btnDelete').removeAttr('disabled');
            } else {
                $('#btnDelete').attr('disabled', 'disabled');
            }
        });

        $('#dialogDeleteBtn').click(function () {
            $('#frmList_ohrmListComponent').submit();
        });

        $("#performanceReview360SearchForm_employeeName").autocomplete(employees, {
            formatItem: function (item) {
                return $('<div/>').text(item.name).html();
            },
            formatResult: function (item) {
                return item.name
            },
            matchContains: true
        }).result(function (event, item) {
        });

        $("#performanceReview360SearchForm_reviwerName").autocomplete(employees, {
            formatItem: function (item) {
                return $('<div/>').text(item.name).html();
            },
            formatResult: function (item) {
                return item.name
            },
            matchContains: true
        }).result(function (event, item) {
            $('#performanceReview360SearchForm_reviwerNumber').val(item.id);
        }
        );






        $('#btnSearch').click(function () {
            $("#empsearch_isSubmitted").val('yes');
            $("#performanceReview360SearchForm_employeeName.inputFormatHint'").val('');
            $("#performanceReview360SearchForm_reviwerName.inputFormatHint'").val('');
            $('#performanceReview360SearchForm').submit();
        });

        $('#btnAdd').click(function () {
            $('#performanceReview360SearchForm').attr("action", "<?php echo public_path('index.php/performance/saveReview'); ?>");
            $('#performanceReview360SearchForm').attr("method", "get");
            $('#performanceReview360SearchForm').submit();
        });

        $('#btnDelete').click(function () {
            $('#frmList_ohrmListComponent').attr("action", "<?php echo public_path('index.php/performance/deleteReview'); ?>");
        });

        $('#btnReset').click(function () {
            $("#empsearch_isSubmitted").val('yes');
            $("#performanceReview360SearchForm_employeeName").val('');
            $("#performanceReview360SearchForm_jobTitleCode").val('');
            $("#performanceReview360SearchForm_reviwerName").val('');
            $("#performanceReview360SearchForm_department").val('0');
            $("#performanceReview360SearchForm_reviwerNumber").val('');
            $("#performanceReview360SearchForm_status").val('0');
            $("#pageNo").val('0');
            $("#fromDate").val('yyyy-mm-dd');
            $("#toDate").val('yyyy-mm-dd');
            $('#performanceReview360SearchForm').submit();
        });

        if ($("#performanceReview360SearchForm_employeeName").val() == '') {
            $("#performanceReview360SearchForm_employeeName").val(typeHint).addClass("inputFormatHint");
        }

        if ($("#performanceReview360SearchForm_reviwerName").val() == '') {
            $("#performanceReview360SearchForm_reviwerName").val(typeHint).addClass("inputFormatHint");
        }
    });



    $("#performanceReview360SearchForm_employeeName").one('focus', function () {
        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
    });

    $("#performanceReview360SearchForm_reviwerName").one('focus', function () {
        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
    });

    function submitPage(pageNo) {
        document.performanceReview360SearchForm.pageNo.value = pageNo;
        document.performanceReview360SearchForm.hdnAction.value = 'paging';
        $('#performanceReview360SearchForm input.inputFormatHint').val('');
        $('#performanceReview360SearchForm input.ac_loading').val('');
        document.getElementById('performanceReview360SearchForm').submit();
    }

</script>