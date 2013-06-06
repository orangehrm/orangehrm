$(document).ready(function() {
   
    $('#btnSave').click(function() {
        
        $('#frmJobCategory').submit();
    });
    
    $('#jobCategory').hide();
    
    $('#btnAdd').click(function() {
        $('#jobCategory').show();
        $('.top').hide();
        $('#jobCategory_name').val('');
        $('#jobCategory_jobCategoryId').val('');
        $('#jobCategoryHeading').html(lang_addJobCat);
        $(".messageBalloon_success").remove();
    });
    
    $('#btnCancel').click(function() {
        $('#jobCategory').hide();
        $('.top').show();
        $('#btnAdd').show();
        validator.resetForm();
    });
    
    
    $('a[href="javascript:"]').click(function(){
		var row = $(this).closest("tr");
		var jobId = row.find('input').val();
		var url = jobCatInfoUrl+jobId;
        $('#jobCategoryHeading').html(lang_editJobCat);
		getJobCatInfo(url);

	});
    
    $('#btnDelete').attr('disabled', 'disabled');

        
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

    $('#btnDelete').click(function(){
        $('#frmList_ohrmListComponent').submit(function(){
            $('#deleteConfirmation').dialog('open');
            return false;
        });
    });

    $('#frmList_ohrmListComponent').attr('name','frmList_ohrmListComponent');
    $('#dialogDeleteBtn').click(function() {
        document.frmList_ohrmListComponent.submit();
    });
    $('#dialogCancelBtn').click(function() {
        $("#deleteConfirmation").dialog("close");
    });
    
    $.validator.addMethod("uniqueName", function(value, element, params) {
        var temp = true;
        var currentJobCat;
        var id = $('#jobCategory_jobCategoryId').val();
        var vcCount = jobCatList.length;
        for (var j=0; j < vcCount; j++) {
            if(id == jobCatList[j].id){
                currentJobCat = j;
            }
        }
        var i;
        vcName = $.trim($('#jobCategory_name').val()).toLowerCase();
        for (i=0; i < vcCount; i++) {

            arrayName = jobCatList[i].name.toLowerCase();
            if (vcName == arrayName) {
                temp = false
                break;
            }
        }
        if(currentJobCat != null){
            if(vcName == jobCatList[currentJobCat].name.toLowerCase()){
                temp = true;
            }
        }
		
        return temp;
    });
    
    var validator = $("#frmJobCategory").validate({

        rules: {
            'jobCategory[name]' : {
                required:true,
                maxlength: 50,
                uniqueName: true
            }
        },
        messages: {
            'jobCategory[name]' : {
                required: lang_NameRequired,
                maxlength: lang_exceed50Charactors,
                uniqueName: lang_uniqueName
            }

        }

    });
});

function getJobCatInfo(url){
    
    $.getJSON(url, function(data) {
		$('#jobCategory_jobCategoryId').val(data.id);
		$('#jobCategory_name').val(data.name);
		$('#jobCategory').show();
		$(".messageBalloon_success").remove();
        $('.top').hide();
	});
}