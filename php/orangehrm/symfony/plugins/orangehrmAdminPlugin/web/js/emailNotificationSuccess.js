$(document).ready(function() {
    $('#btnSave').hide()

    var natCount = notificationIdList.length;
    for (var j=0; j < natCount; j++) {
        var chkBox = "#ohrmList_chkSelectRecord_"+notificationIdList[j].id
        $(chkBox).attr('checked', true)
    }
    $(':checkbox').attr('disabled','disabled')

    $('#btnEdit').click(function(){
        $(':checkbox').removeAttr('disabled')
        $('#btnEdit').hide()
        $('#btnSave').show()
    });

//
//    $("#ohrmList_chkSelectAll").click(function() {
//        if($(":checkbox").length == 1) {
//            $('#btnDelete').attr('disabled','disabled');
//        }
//        else {
//            if($("#ohrmList_chkSelectAll").is(':checked')) {
//                $('#btnDelete').removeAttr('disabled');
//            } else {
//                $('#btnDelete').attr('disabled','disabled');
//            }
//        }
//    });
//
//    $(':checkbox[name*="chkSelectRow[]"]').click(function() {
//        if($(':checkbox[name*="chkSelectRow[]"]').is(':checked')) {
//            $('#btnDelete').removeAttr('disabled');
//        } else {
//            $('#btnDelete').attr('disabled','disabled');
//        }
//    });
});
