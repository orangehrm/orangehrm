
<?php if($reportPermissions->canRead()){?>
<div class="box toggableForm">
   
    <div class="head">
        <h1><?php echo __("Employee Reports"); ?></h1>
    </div>
    <div class="inner" >
        <form action="<?php echo url_for("core/viewDefinedPredefinedReports"); ?>" id="searchForm" method="post">
            <?php echo $searchForm['_csrf_token']->render(); ?>
            <fieldset>
                
                <ol>
                    <li>
                    <?php echo $searchForm->render(); ?>
                    </li>
                </ol>
                
                <p>
                   <input type="submit" class="searchBtn" value="<?php echo __('Search') ?>" name="_search" />
                <input type="button" class="reset" value="<?php echo __('Reset') ?>" name="_reset" />
                <?php echo $searchForm->renderHiddenFields(); ?>
                </p>
                
            </fieldset>
        </form>
    </div>
    <a href="#" class="toggle tiptip" title="Expand for options">&gt;</a>
</div>

<?php include_component('core', 'ohrmList', $parmetersForListComponent); ?>

<?php }?>

<!-- Confirmation box HTML: Begins -->
<div class="modal hide" id="deleteConfModal">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
  </div>
  <div class="modal-body">
    <p><?php echo __(CommonMessages::DELETE_CONFIRMATION); ?></p>
  </div>
  <div class="modal-footer">
    <input type="button" class="btn" data-dismiss="modal" id="dialogDeleteBtn" value="<?php echo __('Ok'); ?>" />
    <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
  </div>
</div>
<!-- Confirmation box HTML: Ends -->

<script type="text/javascript">

    var reportList = <?php echo str_replace('&quot;', "'", $reportJsonList); ?>;

    $(document).ready(function(){
        
        $('#frmList_ohrmListComponent').attr('name','frmList_ohrmListComponent');
    
        $('#btnDelete').attr('disabled','disabled');
      
        $("#ohrmList_chkSelectAll").click(function() {
            if($(":checkbox").length == 1) {
                $('#btnDelete').attr('disabled','disabled');
            }
            else {
                if($("#ohrmList_chkSelectAll").is(':checked')) {
                    $('#btnDelete').removeAttr('disabled');
                } else {
                    $('#btnDelete').attr('disabled','disabled');
                }
            }
        });
    
    
        $(':checkbox[name*="chkSelectRow[]"]').click(function() {
            if($(':checkbox[name*="chkSelectRow[]"]').is(':checked')) {
                $('#btnDelete').removeAttr('disabled');
            } else {
                $('#btnDelete').attr('disabled','disabled');
            }
        });
    
        $(".reset").click(function() {
            $("#search_search").val("");
            $('#searchForm').submit();
        });

        $("#search_search").autocomplete(reportList, {

            formatItem: function(item) {
                return unescapeHtml(item.name);
            }
            ,matchContains:true
        }).result(function(event, item) {
        });

        $('#dialogDeleteBtn').click(function() {        
            document.frmList_ohrmListComponent.submit();
        });
    });
    
    function addPredefinedReport(){
        window.location.replace('<?php echo url_for('core/definePredefinedReport'); ?>');
    }

    function unescapeHtml(html) {
        var temp = document.createElement("div");
        temp.innerHTML = html;
        var result = temp.childNodes[0].nodeValue;
        temp.removeChild(temp.firstChild)
        return result;
    }
</script>
