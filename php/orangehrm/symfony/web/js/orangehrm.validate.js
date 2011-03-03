/* All jQuery custom and common validation functions need to be written here
 * please avoid highly customized validation methods
 * you can write them in orangehrm.<module_name>.js as part of refactoring
 *
 * @author sujith
 **/

/**
 * valid_date validator method.
 *
 * validates that date matches given format.
 * Supports validating according to format used by jquery datepicker
 * Needs JQuery UI datePicker to work.
 *
 * @param value string - Value to check
 * @param element DOM Element - Element (not used in validator)
 * @param params Properties object.
 *
 * Required Params: format - date format string.
 *                  displayFormat - date display format
 * Optional Params: required - is value required. Defaults to false.
 *
 * @return boolean true if validated, false if not
 */
$.validator.addMethod("valid_date",
    function(value, element, params) {

    var valid = false;
    var format = params.format;
    var displayFormat = params.displayFormat;
    
    var required = false;
    
    if (typeof params.required != 'undefined') {
        required = params.required;
    }

    try {
        var trimmedValue = $.trim(value);

        // If not required, empty or format is ok.
        if (!required && ((trimmedValue == '') || (trimmedValue == displayFormat)) ) {
            valid = true;
        }
        else {
            var parsedDate = $.datepicker.parseDate(format, trimmedValue);
            if (parsedDate) {
                var formattedDate = $.datepicker.formatDate(format, parsedDate);
                if (trimmedValue == formattedDate) {
                    var year = parsedDate.getFullYear();

                    // Additional validation, since datePicker.parseDate
                    // accepts 3 digit years or very 4 or more digit years.
                    if (year > 1000 & year < 9999) {
                        valid = true;
                    }
                }
            }
        }
    } catch (error) {
        valid = false;
    }

    return valid;

});


//this is to check for valid alpha characters only texts no numbers or symbols
$.validator.addMethod("alpha", function(value, element) {
   return this.optional(element) || /^[a-zA-Z]+$/.test(value);
});

//this is to check for orangeHRM specific date format
$.validator.addMethod("orangehrmdate", function(value, element) {
   var dt = value.toString();
   if(dt == "" || dt.toLowerCase() == "yyyy-mm-dd") {
      return true;
   }
   dt = dt. split("-");
   return validateDate(parseInt(dt[2], 10), parseInt(dt[1], 10), parseInt(dt[0], 10));
});

function validateDate(day, month, year) {
   var days31 = new Array(1,3,5,7,8,10,12);

   if(month > 12 || month < 1) {
      return false;
   }

   if(day == 29 && month == 2) {
      if(year % 4 == 0) {
         return true;
      }
   }

   if(month == 2 && day < 29) {
      return true;
   }

   if(day < 32 && month != 2) {
      if(day == 31) {
         flag = false;
         for(i=0; i < days31.length; i++) {
            if(days31[i] == month) {
               flag = true;
               break;
            }
         }
         return flag;
      }
      return true;
   }
   return false;
}


$.validator.addMethod("isFutureDate", function(value, element) {
    date = new Date();
    //todayStr = date.getFullYear()+"-0"+date.getMonth() +"-"+ date.getDate();
    today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    dob = strToDate(value, "yyyy-MM-dd");
    if(dob > today){
        return false;
    }
    return true;
});

$.validator.addMethod("phone", function(value, element) {
        return (checkPhone(element));
    },
    ""
);