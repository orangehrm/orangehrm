/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
var classStr;
var activityId;
var date;
var cmnt;
var hiddenCommentInput;
$(document).ready(function() {
    var currentId;

    var validator = $("#frmCommentSave").validate({
        rules: {
            'timeComment' : {
                maxlength: 2000
            }
        },
        messages: {
            'timeComment' : {
                maxlength: erorrMessageForInvalidComment
            }
        }
    });

    $(".project").click(function(){
        element = $(this)
        if($(element).val() == typeForHints){
            this.value = "";
            $(this).removeClass("inputFormatHint");
        }
    });
    
    $(".project").focus(function(){
        element = $(this);
        if (element.data('init') != true) {
            initAutoComplete(element);
            element.data('init', true);
        }
    });

    $(".deletedRow").attr("disabled", "disabled");

    $(".project").each(function(){
        element = $(this)
        if($(element).val() == typeForHints){
            $(element).addClass("inputFormatHint");
        }
        $(element).val($(element).val().replace("##", ""));
    });

    //Auto complete
    function initAutoComplete(element) {
        element.autocomplete(projectsForAutoComplete, {
            formatItem: function(item) {
                var temp = $("<div/>").text(item.name).html();
                return temp.replace("##", "");
            },
            formatResult: function(item) {
                var temp = item.name;
                return temp.replace("##", "");
            },
            matchContains:true
        }).result(function(event, item) {
            currentId = $(this).attr('id');
            var temparray = currentId.split('_');
            var temp = '#'+temparray[0]+'_'+temparray[1]+'_'+'projectActivityName';
            var decodedfullName = $("<div/>").text(item.name).html();
           
            var array = decodedfullName.split(' - ##');
    
            var r = $.ajax({
                type: 'POST',
                url: getActivitiesLink,
                data: {
                    projectId: item.id
                },
               
                success: function(msg){
                    $(temp).html(msg);
                    var flag = validateProject();
                    if(!flag) {
                        $('#btnSave').attr('disabled', 'disabled');
                        $('#validationMsg').attr('class', "messageBalloon_failure");
                    }
                    else{
                        $('#btnSave').removeAttr('disabled');
                        $('#validationMsg').removeAttr('class');
                        $('#validationMsg').html("");
                        $(".messageBalloon_success").remove();
                    }
                }
            }).responseText;
        });
    }

    $("#commentSave").click(function() {
        if ($("#frmCommentSave").valid()) {
            var comment = $("#timeComment").val();
            if (comment != '') {
                var timesheetItemId = saveComment(timesheetId, activityId, date, comment, employeeId, $("input#time__csrf_token").val());
                if (hiddenCommentInput != '') {                    
                    $("#"+hiddenCommentInput).val(timesheetItemId); 
                }
            }
            $('#commentDialog').modal('hide');
        }
    });

    $('#timesheetForm').submit(function(){
        var projectFlag = validateProject();
        if(!projectFlag) {
            $('#btnSave').attr('disabled', 'disabled');
            return false;
        }
        var inputFlag = validateInput();
        if(!inputFlag){
            return false;
        }
        var rowFlag = validateRow();
        if(!rowFlag){
            return false;
        }
    });

    function validateInput() {
        var flag = true;
        displayMessages('reset', '');
        var errorStyle = "background-color:#FFDFDF;";
        $('.timeBox').each(function(){
            element = $(this);
            $(element).removeClass('validation-error');
            if($(element).val()){
                if(!(/^[0-9]+\.?[0-9]?[0-9]?$/).test($(element).val())) {
                    var temp = $(element).val().split(":");
                    if(!(/^[0-9]+\.?[0-9]?[0-9]?$/).test(temp[0]) || !(/^[0-9]+\.?[0-9]?[0-9]?$/).test(temp[1])){
                        displayMessages('warning', lang_not_numeric);
                        $(element).addClass('validation-error');
                        flag = false;
                    }else if(temp[0]>23 || temp[1]>59){
                        displayMessages('warning', lang_not_numeric);
                        $(element).addClass('validation-error');
                        flag = false;
                    }
                }
                else  {
                    if(parseFloat($(element).val()) > 24) {
                        displayMessages('warning', lang_not_numeric);
                        $(element).addClass('validation-error');
                        flag = false;
                    }
                }
                if(flag){
                    id=element.attr('id');
                    idArray= id.split("_");
                    var flag1=  validateVerticalTotal(idArray[2]);
                    if(!flag1){
                        $(element).addClass('validation-error');
                        displayMessages('warning', incorrect_total);  
                        flag=false;
                    }
                    else{
                        displayMessages('reset', '');
                    }
                }
            }
        });
        return flag;
    }

    $('.timeBox').change(function() {
        var flag = validateInput();
        if(!flag) {
            $('#btnSave').attr('disabled', 'disabled');
        }
        else{
            $('#btnSave').removeAttr('disabled');
        }
    });
    
    function validateVerticalTotal(id){
        var total=0;
        var error=false;
        for(j=0;j<rows-1;j++){
            if((/^[0-9]+\.?[0-9]?[0-9]?$/).test($("#initialRows_"+j+"_"+id).val())) {
                var temp=parseFloat($("#initialRows_"+j+"_"+id).val());
                total=total+temp;
            } else if ($("#initialRows_"+j+"_"+id).val() == '') {
                total=total;
            } else{
                var temp = $("#initialRows_"+j+"_"+id).val().split(":");
                temp[0]= parseFloat(temp[0]);
                temp[1]= parseFloat(temp[1])
                total=total+(temp[0]*3600+temp[1]*60)/3600;
            }
        }
        if(total>24){
            error=true;
        }
        return !error;
    }


    function validateRow() {
        var flag = true;
        displayMessages('reset', '');
        var errorStyle = "background-color:#FFDFDF; width: 225px;";
        var normalStyle = "background-color:#FFFFFF; width: 225px;";
        var projectActivityElementArray = new Array();
        var index = 0;
        $('.projectActivity').each(function(){
            element = $(this);
            $(element).removeClass('validation-error');
            if($(element).val()==-1){
                $(element).addClass('validation-error');
                displayMessages('warning', please_select_an_activity);  
                flag = false;
            }
            projectActivityElementArray[index] = $(element);
            index++;
        });

        for(var i=0; i<projectActivityElementArray.length; i++){
            var currentElement = projectActivityElementArray[i];
            for(var j=1+i; j<projectActivityElementArray.length; j++){
                if(currentElement.val() == projectActivityElementArray[j].val() ){
                    currentElement.addClass('validation-error');
                    displayMessages('warning', rows_are_duplicate);  
                    projectActivityElementArray[j].addClass('validation-error');
                    flag = false;
                }
            }
        }
        return flag;
    }

    $('.projectActivity').bind('change',(function() {
        var flag = validateRow();
        if(!flag) {
            $('#btnSave').attr('disabled', 'disabled');
            $('#btnSave').attr('background', 'grey')
        }
        else{
            $('#btnSave').removeAttr('disabled');
        }
    
    }));

    function validateProject() {        
        var flag = true;
        displayMessages('reset', '');
        var errorStyle = "background-color:#FFDFDF;";
        var normalStyle = "background-color:#FFFFFF;";
        var projectCount = projectsArray.length;
        $('input.project').each(function(){
            element = $(this);
            $(element).removeClass('validation-error');
            proName = $.trim($(element).val()).toLowerCase();
            var temp = false;
            var i;
            for (i=0; i < projectCount; i++) {
                arrayName = projectsArray[i].name.toLowerCase().replace("##", "");
                arrayName = $("<div/>").text(arrayName).html();
                arrayName = $("<div/>").html(arrayName).text();
                if (proName == arrayName) {
                    
                    temp = true;
                    break;
                }
            }
            if(!temp){
                displayMessages('warning', project_name_is_wrong);
                $(element).addClass('validation-error');
                flag = false;
            }
        });
        return flag;
    }

    $('.commentIcon').click(function(){
        hiddenCommentInput = null;
        $("#commentError").html("");
        $('#frmCommentSave').validate().resetForm();
        $('#timeComment').removeClass('validation-error');
        $("#timeComment").val("");
        classStr = $(this).attr("id").split("_");
        deleteStr = $(this).attr("class").split(" ");
        if(deleteStr[1] == "deletedRow"){
            $("#timeComment").attr("disabled", "disabled");
            $("#commentSave").hide();
        }else{
            $("#timeComment").removeAttr("disabled");
            $("#commentSave").show();
        }
        var rowNo = classStr[2];
        hiddenCommentInput = "initialRows_" + classStr[2] + "_TimesheetItemId" + classStr[1];
        date = currentWeekDates[classStr[1]];
        var activityNameId = "initialRows_"+rowNo+"_projectActivityName";
        activityId = $("#"+activityNameId).val();
        var comment = getComment(timesheetId,activityId,date,employeeId);
        $("#timeComment").val(comment);
        var projectNameId = "initialRows_"+rowNo+"_projectName";
        var activityNameId = "initialRows_"+rowNo+"_projectActivityName";
        var projectName = $.trim($("#"+projectNameId).val()).toLowerCase();
        var errorStyle = "background-color:#FFDFDF;";
        var projectCount = projectsArray.length;
        var temp = false;
        var i;
        for (i=0; i < projectCount; i++) {
            arrayName = projectsArray[i].name.toLowerCase().replace("##", "");
            arrayName = $('<div/>').text(arrayName).html();
            var escapedArrayNameWithlt = arrayName.replace(/\&lt;/g, '<');
            var escapedArrayNameWithgt = escapedArrayNameWithlt.replace(/\&gt;/g, '>');
       
            if (projectName == escapedArrayNameWithgt) {
                temp = true;
                break;
            }
        }
        if($("#"+projectNameId).val()=="" || $("#"+projectNameId).val() == typeForHints || $("#"+activityNameId).val()=='-1'){
            displayMessages('warning', lang_selectProjectAndActivity);  
        } else if( temp==false){
            displayMessages('warning', lang_enterExistingProject);  
        } else {
            $("#commentProjectName").text($("#"+projectNameId).val());
            $("#commentActivityName").text($("#"+activityNameId+" :selected").text());
            var parsedDate = $.datepicker.parseDate("yy-mm-dd", date);
            $("#commentDate").text($.datepicker.formatDate(datepickerDateFormat, parsedDate));
            $("#commentDialog").modal();
        }
    });

    function saveComment(timesheetId,activityId,date,comment,employeeId,csrfToken) {
        var data = 'timesheetId=' + timesheetId + '&activityId=' + activityId + '&date=' + date+ '&comment=' + encodeURIComponent(comment)+ '&employeeId=' + employeeId + '&csrfToken=' + csrfToken;
        var r=$.ajax({
            type: 'POST',
            url: commentlink,
            data: data,
            async: false
        }).responseText;
        return r;
    }

    function getComment(timesheetId, activityId, date, employeeId){
        var r = $.ajax({
            type: 'POST',
            url: linkToGetComment,
            data: "timesheetId="+timesheetId+"&activityId="+activityId+"&date="+date+"&employeeId="+employeeId,
            async: false,
            success: function(comment){
                cmnt= comment;
            }
        });
        return cmnt;
    }
});





