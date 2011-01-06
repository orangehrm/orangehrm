/*
 * JQuery Date picker plugin with Holidays and Days of marker
 * This function is based on Jquery
 * 
*/
function DayMarker(optionList){
    
    var options = {
        holidayListAjax : '/leave/getHolidayAjax',
        daysOffListAjax : '/leave/getWorkWeekAjax',
        autoFetchHolidays: true,
        autoFetchDaysOff: true,
        requestUrl: false // is request url defined
    };

    var holidayList = []; // store holiday list Public method
    var daysOffList = []; // store holiday list
    var jqValidatorDateFormat = "yy-mm-dd"

    var requestUrl ="";

    var d = new Date(); // Private date  - current date
    var currentYear = d.getFullYear();
    
    
    // privtate function
    var setHolidayList = function(){
        $.ajax({
            url: requestUrl + options.holidayListAjax,
            cache: false,
            data : "year="+currentYear,
            dataType: 'json',
            success: function(hList){
                holidayList = hList;
            },
            error:function(request){
                holidayList = [];
            }
        });
    }

    var setDaysOffList = function(){
        $.ajax({
            url: requestUrl + options.daysOffListAjax,
            cache: false,
            dataType: 'json',
            success: function(hList){
                daysOffList = hList;
            },
            error:function(request){
                daysOffList = [];
            }
        });
    }

    var setRequestUrl = function(){
        pathArray = document.location.href.split( '/' );
        newPathname = "";
        for ( i = 0; i < pathArray.length; i++ ) {
            if(i>0){
                newPathname += "/";
            }

            newPathname += pathArray[i];
            if(pathArray[i] == 'index.php'){
                break;
            }
        }
        requestUrl = newPathname;
    }
    
    var markDates = function(date){

        for (i = 0; i < holidayList.length; i++) {

            if (date.getMonth() == holidayList[i][1] - 1
                && date.getDate() == holidayList[i][2]
                && date.getFullYear() == holidayList[i][0]) {
                if(holidayList[i][3]=='f'){
                    return [true, "ui-state-fullday", ""];
                }
                else{
                    return [true, "ui-state-halfday", ""];
                }
            }
            else{
                // mark repeated dates
                if(date.getFullYear() > holidayList[i][0]){

                    if(date.getMonth() == holidayList[i][1] - 1
                        && date.getDate() == holidayList[i][2] && holidayList[i][4]==1){
                        //console.log(date.getFullYear());
                        if(holidayList[i][3]=='f'){
                            return [true, "ui-state-fullday", ""];
                        }
                        else{
                            return [true, "ui-state-halfday", ""];
                        }
                    }
                }
            }

        }

        for(i=0; i< daysOffList.length; i++){
            // Sunday is known as 0 in jQuery date picker
            daysOffList[i][0] = (daysOffList[i][0] == 7)?0:daysOffList[i][0];
                    
            if (date.getDay() == daysOffList[i][0]) {
                if(daysOffList[i][1]=='w'){
                    return [true, "ui-state-daysoff-weekend", ""];
                }
                else{
                    return [true, "ui-state-daysoff-halfday", ""];
                }

            }
        }

        return [true, ""]
    }

    /*
     * Bind the element in to the j jQuery datepicker
     */
    this.bindElement = function(input, options){
        // default properties of jQuery datepicker object
        defaults = {
            dateFormat: jqValidatorDateFormat,
            changeMonth: true,
            changeYear: true,
            numberOfMonths: [1, 1],
            showCurrentAtPos: 0,
            firstDay: 0,
            beforeShowDay: function (date){
                return markDates(date);
            }
        }

        // overide date picker default properties
        $.extend(defaults, options);

        $(input).datepicker(
            defaults
        );


    }

    /*
     * Show the jQuery datepicker
     */
    this.show = function(button){
        $(button).datepicker('show');
    }
    
   
    /*
     * get Holiday List
     *
     * @public
     *
     */
    this.getHolidayList = function(){
        return holidayList;
    }

    /*
     * get days Off List
     * @public
     */
    this.getdaysOffList = function(){
        return daysOffList;
    }

    /*
     * set Holiday List
     *
     * @public
     *
     */
    this.setHolidayList = function(holidays){
        holidayList = holidays
    }

    /*
     * set days Off List
     * @public
     */
    this.setdaysOffList = function(daysOff){
        daysOffList = daysOff
    }

    setRequestUrl();

    if ( options.autoFetchHolidays ) {
        setHolidayList();
    }
    if ( options.autoFetchDaysOff ) {
        setDaysOffList();
    }
    
}

// create DayMarker Object
var daymarker = new DayMarker();
