/**
 * This file was taken from XPlanner (http://www.xplanner.org).
 * See license/3rdParty/xplanner.license for the license.
 *
 * Modifications done by OrangeHRM
 *
 * 1) New function strToTime() was added.
 */

var dateFormatChars = "dMyhHmsa";

function formatDate(date, format) {
    return formatDate2(date, format, 0);
}

function formatDate2(date, format, offset) {
    if (offset >= format.length) {
        return "";
    } else if (dateFormatChars.indexOf(format.charAt(offset)) != -1) {
        return formatDateElement(date, format, offset);
    } else {
        return formatDateLiteral(date, format, offset);
    }
}

function formatDateElement(date, format, offset) {
    var end = offset;
    var ch = format.charAt(offset);
    while (++end < format.length && format.charAt(end) == ch);
    var count = end - offset;
    var value;
    if (ch == 'd') {
        value = padValue(count, date.getDate());
    }
    else if (ch == 'M') {
        value = padValue(count, date.getMonth()+1);
    }
    else if (ch == 'y') {
        value = padValue(count, date.getFullYear());
    }
    else if (ch == 'H') {
        value = padValue(count, date.getHours());
    }
    else if (ch == 'h') {
        value = padValue(count, date.getHours() % 12);
    }
    else if (ch == 'm') {
        value = padValue(count, date.getMinutes());
    }
    else if (ch == 's') {
        value = padValue(count, date.getSeconds());
    }
    else if (ch == 'a') {
        value = date.getHours() > 12 ? 'PM' : 'AM';
    }
    return value + formatDate2(date, format, end);
}

function padValue(count, value) {
    for (c = value.toString().length; c < count; c++) {
        value = '0'+value;
    }
    return value;
}

function formatDateLiteral(date, format, offset) {
    end = offset;
    while (++end < format.length && dateFormatChars.indexOf(format.charAt(end)) == -1);
    return format.substr(offset, end - offset) + formatDate2(date, format, end);
}

function strToTime(str, format) {

	yearVal = '';
	monthVal = '';
	dateVal = '';
	hourVal = '';
	minuteVal = '';
	aVal = '';

	j=0;
	for (i=0; i<format.length; i++) {

		ch = format.charAt(j);
		sCh = str.charAt(i);

		if (ch == 'd') {
	        dateVal = dateVal.toString()+sCh;
	    } else if (ch == 'M') {
	        monthVal = monthVal.toString()+sCh;
	    } else if (ch == 'y') {
	        yearVal = yearVal.toString()+sCh;
	    } else if (ch == 'H') {
	    	hourVal = hourVal.toString()+sCh;
	    } else if (ch == 'h') {
	        hourVal = hourVal.toString()+sCh;
	        if (hourVal > 12) return false;
	    } else if (ch == 'm') {
	        minuteVal = minuteVal.toString()+sCh;
	    } else if (ch == 'a') {
	        sCh = sCh+str.charAt(i+1);
	        if (sCh == 'PM') {
	        	hourVal+=12;
	        } else if (sCh != 'AM') {
	        	return false;
	        }
	        i++;
	    } else {
	    	if (ch != sCh) {
	    		return false;
	    	}
	    }
	    j++;
	}

	if ((monthVal < 1) || (monthVal > 12) || (dateVal < 1) || (dateVal > 31) || (hourVal > 24) || (minuteVal > 59)) {
		return false;
	}
	date = new Date(yearVal, monthVal-1, dateVal, hourVal, minuteVal);

	return date.getTime();

}

function strToDate(str, format) {

	yearVal = '';
	monthVal = '';
	dateVal = '';

	for (i=0; i<format.length; i++) {

		ch = format.charAt(i);
		sCh = str.charAt(i);

		if (ch == 'd') {
	        dateVal = dateVal.toString()+sCh;
	    } else if (ch == 'M') {
	        monthVal = monthVal.toString()+sCh;
	    } else if (ch == 'y') {
	        yearVal = yearVal.toString()+sCh;
	    } else {
	    	if (ch != sCh) {
	    		return false;
	    	}
	    }
	}

	if ((monthVal < 1) || (monthVal > 12) || (dateVal < 1) || (dateVal > 31)) {
		return false;
	}
	date = new Date(yearVal, monthVal-1, dateVal);

	return date.getTime();
}
