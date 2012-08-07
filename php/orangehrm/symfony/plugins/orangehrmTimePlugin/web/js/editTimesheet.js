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
$(document).ready(function() {
    var currentId;

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
                var temp = $("<div/>").html(item.name).text();
                return temp.replace("##", "");
            }
            ,
            matchContains:true
        }).result(function(event, item) {
    
            currentId = $(this).attr('id');
    
            var temparray = currentId.split('_');
            var temp = '#'+temparray[0]+'_'+temparray[1]+'_'+'projectActivityName';
            var decodedfullName = $("<div/>").html(item.name).text();
           
            var array = decodedfullName.split(' - ##');
    
            var r = $.ajax({
                type: 'POST',
                url: getActivitiesLink,
                data: {
                    customerName: array[0],
                    projectName: array[1]
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
        }
        );
    }

    $("#commentDialog").dialog({
        autoOpen: false,
        width: 350,
        height: 235
    });

    $("#commentCancel").click(function() {
        $("#commentDialog").dialog('close');
    });

    $("#commentSave").click(function() {

        if(validateTimehseetItemComment()){
		
            $("#commentError").html("");
            var comment = $("#timeComment").val();

   
            saveComment(timesheetId, activityId, date, comment, employeeId);
            $("#commentDialog").dialog('close');
        }
	
    });

    $(".plainbtn").click(function(e){
        $(this).addClass("e-clicked");
    });

    $('#timesheetForm').submit(function(){
        $('#validationMsg').removeAttr('class');
        if( $(this).find(".e-clicked").attr("id") == "submitSave" ){
            var projectFlag = validateProject();
            if(!projectFlag) {
                $('#btnSave').attr('disabled', 'disabled');
                $('#validationMsg').attr('class', "messageBalloon_failure");
                return false;
            }
            var inputFlag = validateInput();
            if(!inputFlag){
                $('#validationMsg').attr('class', "messageBalloon_failure");
                return false;
            }
            var rowFlag = validateRow();
            if(!rowFlag){
                $('#validationMsg').attr('class', "messageBalloon_failure");
                return false;
            }
        }
        if( $(this).find(".e-clicked").attr("id") == "submitRemoveRows" ){
            var deleteFlag = false;
            $('.toDelete').each(function(){
                element = $(this);
                if($(element).is(':checked')){
                    deleteFlag =  true;
                }
            });
            if(!deleteFlag){
                $('#validationMsg').html(select_a_row);
                $('#validationMsg').attr('class', "messageBalloon_failure");
            }
            return deleteFlag;
        }
        $( this ).find("input[type=\"submit\"]").removeClass("e-clicked");

    });
    function validateInput() {
		
        var flag = true;
        $(".messageBalloon_success").remove();
        $('#validationMsg').removeAttr('class');
        $('#validationMsg').html("");

        var errorStyle = "background-color:#FFDFDF;";
        $('.items').each(function(){
            element = $(this);
            $(element).removeAttr('style');
    
            if($(element).val()){
                if(!(/^[0-9]+\.?[0-9]?[0-9]?$/).test($(element).val())) {
                    var temp = $(element).val().split(":");
                    if(!(/^[0-9]+\.?[0-9]?[0-9]?$/).test(temp[0]) || !(/^[0-9]+\.?[0-9]?[0-9]?$/).test(temp[1])){
                        $('#validationMsg').html(lang_not_numeric);
                        $(element).attr('style', errorStyle);
                        flag = false;
                    }else if(temp[0]>23 || temp[1]>59){
                        $('#validationMsg').html(lang_not_numeric);
                        $(element).attr('style', errorStyle);
                        flag = false;
                    }

                }

                else  {
                    if(parseFloat($(element).val()) > 24) {
                        $('#validationMsg').html(lang_not_numeric);
                        var errorStyle = "background-color:#FFDFDF;";
                        $(element).attr('style', errorStyle);
                        flag = false;
                    }
                }

            
            
                if(flag){
          
                    id=element.attr('id');
                    idArray= id.split("_");
                    var errorStyle = "background-color:#FFDFDF;";
                    var flag1=  validateVerticalTotal(idArray[2]);
                 
                    if(!flag1){
                        $('#validationMsg').html(incorrect_total);
                        $(element).attr('style', errorStyle);
                                         
                        flag=false;
                    }
                    else{
                        $(".messageBalloon_success").remove();
                        $('#validationMsg').removeAttr('class');
                    }
                                            
                
                }
            }
        });

        return flag;
    }

    $('.items').change(function() {
        var flag = validateInput();
        if(!flag) {
           
            $('#btnSave').attr('disabled', 'disabled');
            $('#validationMsg').attr('class', "messageBalloon_failure");
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
        $(".messageBalloon_success").remove();
        //$(".messageBalloon_failure").remove()
        $('#validationMsg').removeAttr('class');
        $('#validationMsg').html("");

        var errorStyle = "background-color:#FFDFDF; width: 225px;";
        var normalStyle = "background-color:#FFFFFF; width: 225px;";
        var projectActivityElementArray = new Array();
        var index = 0;

        $('.projectActivity').each(function(){
            element = $(this);
            $(element).attr('style', normalStyle);
            if($(element).val()==-1){
                $('#validationMsg').html(please_select_an_activity);
                $(element).attr('style', errorStyle);
                flag = false;
            }
            projectActivityElementArray[index] = $(element);
            index++;
        });

        for(var i=0; i<projectActivityElementArray.length; i++){
            var currentElement = projectActivityElementArray[i];
            for(var j=1+i; j<projectActivityElementArray.length; j++){
                if(currentElement.val() == projectActivityElementArray[j].val() ){
                    currentElement.attr('style', errorStyle);
                    $('#validationMsg').html(rows_are_duplicate);
                    projectActivityElementArray[j].attr('style', errorStyle);
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
            $('#validationMsg').attr('class', "messageBalloon_failure");
        }
        else{
            $('#btnSave').removeAttr('disabled');
        }
    
    }));

    function validateProject() {

        var flag = true;
        $(".messageBalloon_success").remove();
        $('#validationMsg').removeAttr('class');
        $('#validationMsg').html("");

        var errorStyle = "background-color:#FFDFDF;";
        var normalStyle = "background-color:#FFFFFF;";
        var projectCount = projectsArray.length;
        $('input.project').each(function(){
            element = $(this);
            $(element).attr('style', normalStyle);
            proName = $.trim($(element).val()).toLowerCase();
            var temp = false;
            var i;
            for (i=0; i < projectCount; i++) {
                arrayName = projectsArray[i].name.toLowerCase().replace("##", "");
                arrayName = $("<div/>").html(arrayName).text();
                if (proName == arrayName) {
                    
                    temp = true;
                    break;
                }
            }

            if(!temp){
                $('#validationMsg').html(project_name_is_wrong);
                $(element).attr('style', errorStyle);
                flag = false;
            }
        });
        return flag;
    }

    $('#timeComment').keyup(function() {
        
       
        var flag = validateTimehseetItemComment();
        if(!flag) {
            $('#commentSave').attr('disabled', 'disabled');
        //$('#validationMsg').attr('class', "messageBalloon_failure");
        }
        else{
            $('#commentSave').removeAttr('disabled');
            $('#commentCancel').removeAttr('disabled');
            $("#timeComment").removeAttr('style');
        }

    });

    $('.commentIcon').click(function(){

        $("#commentError").html("");
        $("#timeComment").val("");
        classStr = $(this).attr("id").split("_");
        deleteStr = $(this).attr("class").split(" ");
        
        if(deleteStr[1] == "deletedRow"){
            $("#timeComment").attr("disabled", "disabled")
            $("#commentSave").hide()
        }else{
            $("#timeComment").removeAttr("disabled")
            $("#commentSave").show()
        }
        var rowNo = classStr[2];
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
            arrayName = $("<div/>").html(arrayName).text();
       
            if (projectName == arrayName) {
                temp = true;
                break;
            }
        }

        if($("#"+projectNameId).val()=="" || $("#"+projectNameId).val()=="Type for hints..." || $("#"+activityNameId).val()=='-1'){
            $('#validationMsg').attr('class', "messageBalloon_failure");
            $('#validationMsg').html(lang_selectProjectAndActivity);
        } else if( temp==false){
            $('#validationMsg').attr('class', "messageBalloon_failure");
            $('#validationMsg').html(lang_enterExistingProject);
        }else{
            $("#commentProjectName").text(":"+" "+$("#"+projectNameId).val());
            $("#commentActivityName").text(":"+" "+$("#"+activityNameId+" :selected").text());
            var parsedDate = $.datepicker.parseDate("yy-mm-dd", date);
            $("#commentDate").text(":"+" "+$.datepicker.formatDate(datepickerDateFormat, parsedDate));
            $("#commentDialog").dialog('open');
        }

    });


    function validateTimehseetItemComment(){

        errFlag1 = false;


        $('#commentError').html("");

        var errorStyle = "background-color:#FFDFDF;";

        if ($('#timeComment').val().length > 2000) {
            $('#commentSave').attr('disabled', 'disabled');
            $('#commentCancel').attr('disabled', 'disabled');
            //   $('#validationMsg').attr('class', "messageBalloon_failure");
            $('#commentError').html(erorrMessageForInvalidComment);
            $('#timeComment').attr('style', errorStyle);

            errFlag1 = true;
        }

        return !errFlag1;


    }

    function saveComment(timesheetId,activityId,date,comment,employeeId) {

        var data = 'timesheetId=' + timesheetId + '&activityId=' + activityId + '&date=' + date+ '&comment=' + encodeURIComponent(comment)+ '&employeeId=' + employeeId;

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





