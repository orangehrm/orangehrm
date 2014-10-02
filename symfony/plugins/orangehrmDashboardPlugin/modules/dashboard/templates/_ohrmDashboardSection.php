<?php
if ($loadDefault == 'true') {
    echo html_entity_decode($result, ENT_QUOTES, "UTF-8");
} else {
    $divId = $module_name . '__' . $action_name;
    ?>

    <div class="loadmask" style="height: 100%;" id="<?php
         echo $divId;
         ?>">
    </div>
    <script type="text/javascript">

        $(document).ready(function () {
            var moduleUrl = '<?php echo url_for($module_name . '/' . $action_name); ?>';
            var divId = '<?php echo $divId; ?>';
            $.ajax({
                url: moduleUrl,
                success: function (obj) {
                    $("#" + divId).html(obj);
                },
                complete: function () {
                    $("#" + divId).removeClass('loadmask');
                }
            });
        });
    </script>
<?php
}?>