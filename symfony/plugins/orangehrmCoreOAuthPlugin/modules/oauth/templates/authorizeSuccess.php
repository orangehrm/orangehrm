<div class="box">

    <div class="head">
        <h1><?php echo __('Authorize Request'); ?></h1>
    </div>

    <div class="inner">      
        <form id="oAuthApprovalForm" method="post" action="<?php echo $action; ?>" 
              enctype="multipart/form-data">
            <fieldset>
                <ol>
                    <li>
                        <?php echo "<b>" . $client_id . "<b> is requesting your authorization."; ?>
                    </li>
                    <li>
                        <?php echo __("Would you like to authorize the request?"); ?>
                    </li>
                    <input id="authorize" type="hidden" value="0" name="authorize">
                </ol>
                <p>
                    <input type="submit" class="" id="btnAuthorize" value="<?php echo __("Authorize"); ?>" onclick='$("input#authorize").val(1);' />
                    <input type="submit" class="cancel" id="btnCancel" value="<?php echo __("Do not Authorize"); ?>"  />
                </p>
            </fieldset>
        </form>
    </div>
    
</div>
