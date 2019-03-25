

<style>
    svg path,
    svg rect{
        fill: #FF6700;
    }
    .svgcl{
        position: relative;
        left: -35px;
        top: -31px;
    }
    <?php echo $page['css']; ?>
</style>

<script language="javascript">
    var inputDatePattern = '<?php  echo($inputDatePattern) ?>' ;
    var separatorString = '<?php echo __js('to') ?>';
    $( document ).ready(function() {

        $("#loader-1").hide();
        empId = location.href[location.href.length-1];
        dates = $('#startDates').find(":selected").text().split(" "+separatorString+" ");
        startDate_timesheet = dates[0]+" 00:00:00";
        endDate_timesheet   = dates[1]+" 00:00:00";

        clientId  =     "<?php echo  htmlspecialchars_decode($page['id']); ?>";
        clientSecret  = "<?php echo  htmlspecialchars_decode($page['secret']); ?>";
        clientUrl     = "<?php echo  htmlspecialchars_decode($page['url']); ?>";
        successUrl  = "<?php echo  htmlspecialchars_decode($page['successUrl']); ?>";
        ajaxURL = "<?php echo url_for(htmlspecialchars_decode($page['ajaxUrl'])); ?>";
        var timeSheetStatus = $('#timesheet_status').find('h2').text();
        if(timeSheetStatus == 'Status: Approved'){

            $('.syncToggl').hide();
        } else {
            $('.syncToggl').show();
        }

    });

    <?php echo htmlspecialchars_decode($page['js']); ?>
    
    function ajaxSyc() {
        $("#loader-1").show();

        $.ajax({
                type: "POST",
                url: ajaxURL,
                data: {
                    'employee_Id':employeeId,
                    'startTime': startDate_timesheet,
                    'endTime': endDate_timesheet,
                    'timeFormat': inputDatePattern,
                    'timeZone': 'GMT'+formatTimeZone()
                },
                contentType: "application/x-www-form-urlencoded",

                success: function (msg, status, jqXHR) {

                    $("#loader-1").hide();
                    msg = JSON.parse(msg);
                    msgCode = msg.statusCode;
                    if (msgCode != null) {
                        if (msgCode == 101) {
                            displayMessages('error',msg.description );
                        } else if (msgCode == 102) {

                            displayMessages('success', msg.description);
                            setTimeout(function () {
                                location.reload();
                            }, 2000);

                        }
                    } else {
                        showErrorMsg();
                    }

                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    $("#loader-1").hide();
                    console.log(errorThrown);
                    showErrorMsg();
                }
            });
    }
    
    function startSyc() {
        $("#loader-1").show();

    $.ajax({

        type: "POST",
        url: clientUrl,


        data: {
            'grant_type': 'client_credentials',
            'client_id': clientId,
            'client_secret': clientSecret
        },
        contentType: "application/x-www-form-urlencoded",


        success: function (msg, status, jqXHR) {

            try {

                msg = $.parseJSON(jqXHR.responseText);

            } catch (err) {
                console.log(err);
                showErrorMsg();
            }

            $.ajax({
                type: "POST",
                url: successUrl,
                beforeSend: function (xhr) {

                    xhr.setRequestHeader("Authorization", "Bearer " + msg.access_token);
                },

                data: {

                    'employee_Id':employeeId,
                    'startTime': startDate_timesheet,
                    'endTime': endDate_timesheet,
                    'timeFormat': inputDatePattern,
                    'timeZone': 'GMT'+formatTimeZone()
                },
                contentType: "application/x-www-form-urlencoded",

                success: function (msg, status, jqXHR) {

                    $("#loader-1").hide();
                    msgCode = msg.statusCode;
                    if (msgCode != null) {
                        if (msgCode == 101) {
                            displayMessages('error',msg.description );
                        } else if (msgCode == 102) {

                            displayMessages('success', msg.description);
                            setTimeout(function () {
                                location.reload();
                            }, 2000);

                        }
                    } else {
                        showErrorMsg();
                    }

                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    $("#loader-1").hide();
                    console.log(errorThrown);
                    showErrorMsg();
                }
            });

        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $("#loader-1").hide();
            console.log(errorThrown);
            showErrorMsg();
        }


    });

    }

    function showErrorMsg(){
        displayMessages('error','Unable To Sync With Toggl' );
        setTimeout(function () {
            $('#msgDiv').remove();
        }, 3000);

    }

</script>

<div>
    <div class ='toggl'>
    <?php echo html_entity_decode($page['html']); ?>

        <div class="loader loader--style2" title="1">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="loader-1" class="svgcl" x="0px" y="0px" width="30px" height="30px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
  <path fill="#000" d="M25.251,6.461c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615V6.461z">
      <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"/>
  </path>
  </svg>
        </div>
    </div>

    <div class="modal hide" id="togglConfirm" style="display: none;">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">×</a>
            <h3>Confirm Toggl Sync</h3>
        </div>
        <div class="modal-body">
            <p>Any existing timesheet entry will be overwritten if record for same date is matched. Click ok to continue.</p>
        </div>
        <div class="modal-footer">
            <?php if(!empty(htmlspecialchars_decode($page['id']))) {  ?>
                <input id="" onclick="startSyc()" class="" data-dismiss="modal" value="Ok" type="button">
            <?php } else { ?>
                <input id="" onclick="ajaxSyc()" class="" data-dismiss="modal" value="Ok" type="button">
            <?php } ?>
            <input id="addCancel" class="reset" data-dismiss="modal" value="Cancel" type="button">
        </div>
    </div>

</div>



