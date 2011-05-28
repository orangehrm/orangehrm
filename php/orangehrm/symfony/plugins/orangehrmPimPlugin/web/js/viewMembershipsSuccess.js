$(document).ready(function() {
    //Load default Mask if empty
    var cDate = trim($("#membership_subscriptionCommenceDate").val());
    if (cDate == '') {
        $("#membership_subscriptionCommenceDate").val(dateDisplayFormat);
    }

    //Bind date picker
    daymarker.bindElement("#membership_subscriptionCommenceDate",
    {
        onSelect: function(date){
        //$("#membership_subscriptionCommenceDate").valid();
        },
        dateFormat:jsDateFormat
    });

    $('#cDateBtn').click(function(){
        daymarker.show("#membership_subscriptionCommenceDate");

    });

    //Load default Mask if empty
    var rDate = trim($("#membership_subscriptionRenewalDate").val());
    if (rDate == '') {
        $("#membership_subscriptionRenewalDate").val(dateDisplayFormat);
    }

    //Bind date picker
    daymarker.bindElement("#membership_subscriptionRenewalDate",
    {
        onSelect: function(date){
        //$("#membership_subscriptionCommenceDate").valid();
        },
        dateFormat:jsDateFormat
    });

    $('#rDateBtn').click(function(){
        daymarker.show("#membership_subscriptionRenewalDate");

    });

    $("#checkAll").click(function(){
        if($("#checkAll:checked").attr('value') == 'on') {
            $(".checkbox").attr('checked', 'checked');
        } else {
            $(".checkbox").removeAttr('checked');
        }
    });

    if($(".checkbox").length > 1) {
        $(".paddingLeftRequired").hide();
        $("#addPaneMembership").hide();
    } else {
        $("#btnCancel").hide();
        $(".paddingLeftRequired").show();
        $("#addPaneMembership").show();
        $("#listMembershipDetails").hide();
    }

    $(".checkbox").click(function() {
        $("#checkAll").removeAttr('checked');
        if(($(".checkbox").length - 1) == $(".checkbox:checked").length) {
            $("#checkAll").attr('checked', 'checked');
        }
    });

     // Cancel in add pane
        $('#btnCancel').click(function() {
            clearAddForm();
            $('#addPaneMembership').css('display', 'none');
            $('#listActions').show();
            $('#mem_list td.check').show();
            addEditLinks();
            $('div#messagebar').hide();
            $(".paddingLeftRequired").hide();
        });


    // Add a emergency contact
    $('#btnAddMembershipDetail').click(function() {
        $("#membershipHeading").text(addMembershipDetail);
        $(".paddingLeftRequired").show();
        clearAddForm();

        // Hide list action buttons and checkbox
        $('#listActions').hide();
        $('#mem_list td.check').hide();
        removeEditLinks();
        $('div#messagebar').hide();
        $('#addPaneMembership').css('display', 'block');
    });

    $('#btnSaveMembership').click(function() {
        $('#frmEmpMembership').submit();
    });

    $('#delMemsBtn').click(function() {
        var checked = $('#frmEmpDelMemberships input:checked').length;

        if (checked == 0) {
            $("#messagebar").attr("class", "messageBalloon_notice");
            $("#messagebar").text(deleteError);
        } else {
            $('#frmEmpDelMemberships').submit();
        }
    });



});

function clearAddForm() {
    $('#membership_membershipType').val('');
    $('#membership_membership').val('');
    $('#membership_subscriptionPaidBy').val('');
    $('#membership_subscriptionAmount').val('');
    $('#membership_subscriptionCommenceDate').val('');
    $('#membership_subscriptionCommenceDate').val(dateDisplayFormat);
    $('#membership_subscriptionRenewalDate').val(dateDisplayFormat);
    //$('div#addPaneEmgContact label.error').hide();
    $('div#messagebar').hide();
}

function addEditLinks() {
    // called here to avoid double adding links - When in edit mode and cancel is pressed.
    removeEditLinks();
    $('#mem_list tbody td.memshipCode').wrapInner('<a href="#"/>');
}

function removeEditLinks() {
    $('#mem_list tbody td.memshipCode a').each(function(index) {
        $(this).parent().text($(this).text());
    });
}


