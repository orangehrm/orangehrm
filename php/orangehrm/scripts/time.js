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

function strToTime(str) {
	format = /^\s*([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2})\s*$/;

	if (!format.test(str)) return false;

	timeArr = format.exec(str);

	date = new Date(timeArr[1], timeArr[2]-1, timeArr[3], timeArr[4], timeArr[5]);

	return date.getTime();
}