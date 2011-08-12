$(document).ready(function()
    {
        
        $("#commentDialog").dialog({
            autoOpen: false,
            width: 350,
            height: 300
        });
        
        
        
        
        $(".nonEditable").each(function(){
            element = $(this)

            $(".nonEditable").attr("disabled", "disabled");
        
        });
           
        var InDate = trim($(".inDate").val());
        if (InDate == '') {
            $(".inDate").val(dateDisplayFormat);
        }

        //Bind date picker
        daymarker.bindElement(".inDate",
        {
            onSelect: function(date){
              
                $(".inDate").trigger('change');            

            },
            dateFormat:jsDateFormat
        });

 
        var OutDate = trim($(".outDate").val());
        if (OutDate == '') {
            $(".outDate").val(dateDisplayFormat);
        }

        //Bind date picker
        daymarker.bindElement(".outDate",
        {
            onSelect: function(date){
              
                $(".outDate").trigger('change');            

            },
            dateFormat:jsDateFormat
        });

 
       
       
       
        $(".cancel").click(function() {
           
            // getRelatedAttendanceRecords(employeeId,date);
        
            $('form#employeeRecordsForm').attr({
                action:linkToView+"?employeeId="+employeeId+"&date="+date+"&trigger="+true
            });
            $('form#employeeRecordsForm').submit();
        });
        $(".save").click(function() {
           
            // getRelatedAttendanceRecords(employeeId,date);
        
            $('form#employeeRecordsForm').attr({
                action:linkToEdit+"?employeeId="+employeeId+"&date="+date
            });
            $('form#employeeRecordsForm').submit();
        });
        
        
        
        $(".inDate").change(function(){
            element = $(this)
            alert(element.attr('id'));
            
            
            alert(element.val());
        //  alert($("inDate").val());
            
            
        });
        
        $(".icon").click(function() {
           

            $("#noteError").html("");
            $("#punchInOutNote").val("");
            classStr = $(this).attr("id").split("_");
            
         
            if(classStr[1]==2){
                $("#punchInOutNote").attr("disabled","disabled");
                $("#commentSave").hide();
            }
            else{
                 $("#commentSave").show();
                $("#punchInOutNote").attr("disabled","");
            }
  
            $("#punchInOutNote").val($("#attendanceNote_"+classStr[1]+"_"+classStr[2]+"_"+classStr[0]).val());
       
            $("#commentDialog").dialog('open');


        });
        
           $("#commentCancel").click(function() {
                $("#commentDialog").dialog('close');
            });

   

    });
