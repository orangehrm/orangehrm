<?php use_stylesheets_for_form($form); ?>

<?php use_stylesheet(plugin_web_path('orangehrmPerformancePlugin', 'css/myPerformanceReviewSuccess.css')); ?> 

<div class="box searchForm toggableForm" id="performance-list-search">
    <div class="head">
        <h1><?php echo __('Search Performance Reviews') ?></h1>
    </div>
    <div class="inner">
        <form id="evaluatePerformanceReview360SearchForm" name="evaluatePerformanceReviewSearchForm" method="post" action="">
            <fieldset>                
                <ol>
                    <?php echo $form->render(); ?>
                    <input type="hidden" name="pageNo" id="pageNo" value="" />
                    <input type="hidden" name="hdnAction" id="hdnAction" value="search" />     
                </ol>                            
                <p>
                    <input type="button" class="applybutton" id="searchBtn" value="<?php echo __('Search'); ?>" title="<?php echo __('Search'); ?>"/> 
                    <input type="button" class="reset" name="_reset" value="<?php echo __('Reset'); ?>" title="<?php echo __('Reset'); ?>" id="btnReset" >
                </p>                
            </fieldset>

        </form>

    </div> 
    <a href="#" class="toggle tiptip" title="<?php echo __(CommonMessages::TOGGABLE_DEFAULT_MESSAGE); ?>">&gt;</a>
</div>


<div id="listDiv">   
    <?php include_component('core', 'ohrmList'); ?>
</div>
<script>
    $(document).ready(function() {
        
          
        var employees = <?php echo str_replace('&#039;', "'", $form->getReviwerAccessibleEmployeeListAsJson()) ?> ;        

        if ($("#evaluatePerformanceReview360SearchForm_employeeName").val() == '') {
            $("#evaluatePerformanceReview360SearchForm_employeeName").val('<?php echo __("Type for hints") . "..."; ?>').addClass("inputFormatHint");            
        }
        
        $("#evaluatePerformanceReview360SearchForm_employeeName").autocomplete(employees, {
            formatItem: function(item) {
                return item.name;
            }
            ,matchContains:true
        }).result(function(event, item) {
        }
    );
       
                            
        $("#evaluatePerformanceReview360SearchForm_employeeName").one('focus', function() {
            if ($(this).hasClass("inputFormatHint")) {
                $(this).val("");
                $(this).removeClass("inputFormatHint");
            }
        }); 
        
        $('#searchBtn').click(function(){              
            $("#evaluatePerformanceReview360SearchForm_employeeName.inputFormatHint").val('');
            $('#evaluatePerformanceReview360SearchForm').submit();
        });   
        
        $('#addBtn').click(function(){
            $('#evaluatePerformanceReview360SearchForm').attr("action", "<?php echo public_path('index.php/performance/saveReview'); ?>");
            $('#evaluatePerformanceReview360SearchForm').attr("method", "get");
            $('#evaluatePerformanceReview360SearchForm').submit();
        });
        
        $('#deleteBtn').click(function(){
            $('#frmList_ohrmListComponent').attr("action", "<?php echo public_path('index.php/performance/deleteReview'); ?>");
            $('#frmList_ohrmListComponent').submit();
        });
        
        $('#btnReset').click(function(){
        $("#evaluatePerformanceReview360SearchForm_employeeName").val('');
        $("#evaluatePerformanceReview360SearchForm_jobTitleCode").val('');
        $("#evaluatePerformanceReview360SearchForm_reviwerName").val('');
        $("#evaluatePerformanceReview360SearchForm_department").val('0');
        $("#evaluatePerformanceReview360SearchForm_status").val('0');
        $("#fromDate").val('yyyy-mm-dd');
        $("#toDate").val('yyyy-mm-dd');
        $('#evaluatePerformanceReview360SearchForm').submit();
        });
    });
    
    function submitPage(pageNo) {
            document.evaluatePerformanceReviewSearchForm.pageNo.value = pageNo;
            document.evaluatePerformanceReviewSearchForm.hdnAction.value = 'paging';
            $('#evaluatePerformanceReview360SearchForm input.inputFormatHint').val('');
            $('#evaluatePerformanceReview360SearchForm input.ac_loading').val('');
            document.getElementById('evaluatePerformanceReview360SearchForm').submit();
        }
</script>