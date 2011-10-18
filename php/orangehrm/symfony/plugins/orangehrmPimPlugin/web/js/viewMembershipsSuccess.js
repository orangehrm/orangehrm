$(document).ready(function() {

    var commenceDate = "";
    $("#checkAllMem").click(function(){
        if($("#checkAllMem:checked").attr('value') == 'on') {
            $(".checkboxMem").attr('checked', 'checked');
        } else {
            $(".checkboxMem").removeAttr('checked');
        }
    });

    if($(".checkboxMem").length > 1) {
        $(".paddingLeftRequired").hide();
        $("#addPaneMembership").hide();
    } else {
        $("#btnCancel").hide();
        $(".paddingLeftRequired").show();
        $("#addPaneMembership").show();
        $("#listMembershipDetails").hide();
    }

    $(".checkboxMem").click(function() {
        $("#checkAllMem").removeAttr('checked');
        if(($(".checkboxMem").length - 1) == $(".checkboxMem:checked").length) {
            $("#checkAllMem").attr('checked', 'checked');
        }
    });

    $('#membership_membershipType').change(function() {
        var r = $.ajax({
            type: "POST",
            url: getMembershipsUrl,
            dataType: "html",
            data: "membershipTypeCode="+$('#membership_membershipType').val(),
            success: function(msg){
                $('#membership_membership').html(msg);

            }
        })
    });
    
    // Edit a membership detail in the list
    $('#frmEmpDelMemberships a').live('click', function() {

        validator.resetForm();

        var row = $(this).closest("tr");
        var primarykey = row.find('input.checkboxMem:first').val();
        var membership = $(this).text();
        var membershipType = row.find("td:nth-child(3)").text();
        var subscriptionPaidBy = row.find("td:nth-child(4)").text();
        var subscriptionAmount = row.find("td:nth-child(5)").text();
        var currency = row.find("td:nth-child(6)").text();
        var subscriptionCommenceDate = row.find("td:nth-child(7)").text();
        var subscriptionRenewalDate = row.find("td:nth-child(8)").text();
   
        $('#membership_membershipType').val(membershipType);
        ajaxCall(primarykey);
        $('#membership_membershipType').attr('disabled', 'disabled');
        $('#membership_membership').attr('disabled', 'disabled');
        $('#membership_subscriptionPaidBy').val(subscriptionPaidBy);
        $('#membership_subscriptionAmount').val(subscriptionAmount);
        $('#membership_currency').val(currency);

        if ($.trim(subscriptionCommenceDate) == '') {
            subscriptionCommenceDate = datepickerDateFormat;
        }
        if ($.trim(subscriptionRenewalDate) == '') {
            subscriptionRenewalDate = datepickerDateFormat;
        }

        $('#membership_subscriptionCommenceDate').val(subscriptionCommenceDate);
        $('#membership_subscriptionRenewalDate').val(subscriptionRenewalDate);

        $(".paddingLeftRequired").show();
        $("#membershipHeading").text(editMembershipDetail);
        $('div#messagebar').hide();
        // hide validation error messages
   
        $('#listActions').hide();
        $('#mem_list td.check').hide();
        $('#addPaneMembership').css('display', 'block');

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


    // Add a membership detail contact
    $('#btnAddMembershipDetail').click(function() {
        
        $('#membership_membershipType').removeAttr('disabled');
        $('#membership_membership').removeAttr('disabled');
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
        $('#membership_membershipType').removeAttr('disabled');
        $('#membership_membership').removeAttr('disabled');
        commenceDate = $('#membership_subscriptionCommenceDate').val();
        $('#frmEmpMembership').submit();
    });

    $('#delMemsBtn').click(function() {
        var checked = $('#frmEmpDelMemberships input:checked').length;
        
        if (checked == 0) {
            $("#messagebar").attr("class", "messageBalloon_notice");
            $("#messagebar").text(deleteError);
            $('div#messagebar').show();
        } else {
            $('#frmEmpDelMemberships').submit();
        }
    });

    var validator = $("#frmEmpMembership").validate({

        rules: {
            'membership[membershipType]' : {
                required: true
            },
            'membership[membership]' : {
                required: true
            },
            'membership[subscriptionAmount]':{
                number: true, 
                min: 0,
                max: 999999999.99
            },
            'membership[subscriptionCommenceDate]' : {
                valid_date: function() {
                    return {
                        format:datepickerDateFormat,
                        required:false
                    }
                }
            },
            'membership[subscriptionRenewalDate]' : {
                valid_date: function() {
                    return {
                        format:datepickerDateFormat,
                        required:false
                    }
                },
                date_range: function() {
                    return {
                        format:datepickerDateFormat,
                        fromDate:commenceDate
                    }
                }
            }
        },
        messages: {
            'membership[membershipType]' : {
                required: selectAMembershipType
                    
            },
            'membership[membership]' :{
                required: selectAMembership
            },
            'membership[subscriptionAmount]':{
                number: validNumberMsg, 
                min: lang_negativeAmount,
                max: lang_tooLargeAmount
            },
            'membership[subscriptionCommenceDate]' : {
                valid_date: validDateMsg
            },
            'membership[subscriptionRenewalDate]' : {
                valid_date: validDateMsg,
                date_range: dateError
            }

        },
        errorPlacement: function(error, element) {
            error.appendTo( element.prev('label') );
        }

    });

});

function clearAddForm() {

    $('#membership_membershipType').val('');
    $('#membership_membership').val('');
    $('#membership_subscriptionPaidBy').val('');
    $('#membership_subscriptionAmount').val('');
    $('#membership_currency').val('');
    $('#membership_subscriptionCommenceDate').val(datepickerDateFormat);
    $('#membership_subscriptionRenewalDate').val(datepickerDateFormat);
    $('div#addPaneMembership label.error').hide();
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

function ajaxCall(primarykey){

    var primaryArray = primarykey.split(" ");

    var r = $.ajax({
        type: "POST",
        url: getMembershipsUrl,
        dataType: "html",
        data: "membershipTypeCode="+$('#membership_membershipType').val()+"&selectedMembership="+primaryArray[2],
        success: function(msg){
            $('#membership_membership').html(msg);
        }
    })
}

