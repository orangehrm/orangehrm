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

});

