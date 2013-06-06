/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function(){

    $("#btnSave").click(function(){
        $('#definePeriod').submit();
    });
        
    $("#definePeriod").validate({
        rules: {
            'time[startingDays]' : {
                required: true
            }
        },
        messages: {
            'time[startingDays]' : {
                required: lang_required
            }
        }
    });
});