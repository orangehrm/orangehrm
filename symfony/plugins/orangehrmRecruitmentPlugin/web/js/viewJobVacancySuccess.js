$(document).ready(function() {

    $('#frmList_ohrmListComponent').attr('name','frmList_ohrmListComponent');
    
    $('#btnDelete').attr('disabled','disabled');
      
    $("#ohrmList_chkSelectAll").click(function() {
        $('table.table input[id^="ohrmList_chkSelectRecord_"]').attr('checked', ($(this).attr('checked') == 'checked'));
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
        document.frmList_ohrmListComponent.submit();
    });

    $('#btnRst').click(function() {

        $('#vacancySearch_jobTitle').val("");
        $('#vacancySearch_jobVacancy').val("");
        $('#vacancySearch_hiringManager').val("");
        $('#vacancySearch_status').val("");
        $('#frmSrchJobVacancy').submit();
    });

    $('#btnSrch').click(function() {
        $('#frmSrchJobVacancy').submit();
    });

    var jobTitle = $('#vacancySearch_jobTitle').val();
    var vacancyId = $('#vacancySearch_jobVacancy').val();
    var hiringManagerId = $('#vacancySearch_hiringManager').val();

    var vcUrl = vacancyListUrl + jobTitle;
    getVacancyListJson(vcUrl, vacancyId)

    var url1 = hiringManagerListUrlForVacancyId + vacancyId;
    getHiringManagerListJson(url1, hiringManagerId);

    if(vacancyId == ""){
        var url = hiringManagerListUrlForJobTitle + jobTitle;
        getHiringManagerListJson(url, hiringManagerId);
    }



    $('#vacancySearch_jobTitle').change(function() {

        var jobTitle = $('#vacancySearch_jobTitle').val();
        var vcUrl = vacancyListUrl + jobTitle;
        var url = hiringManagerListUrlForJobTitle + jobTitle;

        getVacancyListJson(vcUrl)
        getHiringManagerListJson(url);

    });

    $('#vacancySearch_jobVacancy').change(function() {
        var jobTitle = $('#vacancySearch_jobTitle').val();
        var vacancyId = $('#vacancySearch_jobVacancy').val();
        var url = hiringManagerListUrlForVacancyId + vacancyId;
        getHiringManagerListJson(url);
        if(vacancyId == ""){
            url = hiringManagerListUrlForJobTitle + jobTitle;
            getHiringManagerListJson(url);
        }

    });

});

function addJobVacancy(){
    window.location.replace(addJobVacancyUrl);
}

function getHiringManagerListJson(url, para){

    $.getJSON(url, function(data) {

        //var data = $.unique(data1);

        var numOptions = data.length;
        var optionHtml = '<option value="">'+lang_all+'</option>';

        for (var i = 0; i < numOptions; i++) {
            
            // escape name
            var name = $('<div/>').text(data[i].name).html();
            
            if(data[i].id == para){
                optionHtml += '<option selected="selected" value="' + data[i].id + '">' + name + '</option>';
            }else{
                optionHtml += '<option value="' + data[i].id + '">' + name + '</option>';
            }
        }

        $("#vacancySearch_hiringManager").html(optionHtml);

    })

}

function getVacancyListJson(vcUrl, para){
    $.getJSON(vcUrl, function(data) {
        var numOptions = 0;
        if(data != null){
            numOptions = data.length;
        }
        var optionHtml = '<option value="">'+lang_all+'</option>';

        for (var i = 0; i < numOptions; i++) {
            
            // escape name
            var name = $('<div/>').text(data[i].name).html();

            if(data[i].id == para){
                optionHtml += '<option selected="selected" value="' + data[i].id + '">' + name + '</option>';
            }
            else{
                optionHtml += '<option value="' + data[i].id + '">' + name + '</option>';
            }
        }

        $("#vacancySearch_jobVacancy").html(optionHtml);

    })
}