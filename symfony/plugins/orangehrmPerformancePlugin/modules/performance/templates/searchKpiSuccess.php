<?php use_stylesheets_for_form($form); ?>

<div class="box searchForm toggableForm" id="divFormContainer">
    <div class="head">
        <h1><?php echo __('Search Key Performance Indicators'); ?></h1>
    </div>

    <div class="inner">

        <form id="searchKpi" name="searchKpi" method="post" action="">
            <fieldset>
                <ol>
                    <?php echo $form->render(); ?>
                </ol>

                <p>
                    <input type="button" class="addbutton" name="searchBtn" id="searchBtn" value="<?php echo __("Search"); ?>"/>
                </p>
            </fieldset>
        </form>
    </div>

    <a href="#" class="toggle tiptip" title="<?php echo __(CommonMessages::TOGGABLE_DEFAULT_MESSAGE); ?>">&gt;</a>
</div>

<?php include_component('core', 'ohrmList'); ?>
<?php include_partial('global/delete_confirmation'); ?>
<script>
    $(document).ready(function() {
        $('#searchBtn').click(function(){
            $('#searchKpi').submit();
        });   
        
        $('#btnDelete').attr('disabled', 'disabled');
        
        $("#ohrmList_chkSelectAll").change(function() {
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
        
        $('#dialogDeleteBtn').click(function() {
            $('#frmList_ohrmListComponent').submit();
        });
    });
    
    function addKpi(){
        document.location.href = "<?php echo public_path('index.php/performance/saveKpi'); ?>";
    }
</script>