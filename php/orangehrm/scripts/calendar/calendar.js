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
 *
 *
 */

YAHOO.namespace("OrangeHRM.calendar");
YAHOO.namespace("OrangeHRM.calendar.formatHint");
YAHOO.namespace("OrangeHRM.container");

/**
 * Adds the calendar to the dom
 */
YAHOO.OrangeHRM.calendar.init = function () {
	id="cal1", container="cal1Container";
	if (document.getElementById(container)) {
		YAHOO.OrangeHRM.calendar.cal = new YAHOO.widget.Calendar(id, container, {START_WEEKDAY:1,
																				 DATE_FIELD_DELIMITER: "-",
																				 DATE_RANGE_DELIMITER: " ",
																				 MDY_DAY_POSITION: 3,
																				 MDY_MONTH_POSITION: 2,
																				 MDY_YEAR_POSITION: 1,
																				 close: true});
	
		YAHOO.OrangeHRM.calendar.cal.format = 'yyyy-MM-dd';
	
		YAHOO.OrangeHRM.calendar.cal.selectEvent.subscribe(YAHOO.OrangeHRM.calendar.selected, YAHOO.OrangeHRM.calendar.cal, true);
	
		YAHOO.OrangeHRM.calendar.cal.selectedEvent = new YAHOO.util.CustomEvent('CalendarSelected');
	
		YAHOO.OrangeHRM.calendar.cal.hide();
		YAHOO.OrangeHRM.calendar.addHooks();
	}
	if (YAHOO.OrangeHRM.container.wait) {
		YAHOO.OrangeHRM.container.wait.hide();
	}
};

/**
 * Get all the calendar buttons on a page and add handlers
 *
 */
YAHOO.OrangeHRM.calendar.addHooks = function () {
	elements = YAHOO.util.Dom.getElementsByClassName("calendarBtn");

	for (x in elements) {
		YAHOO.OrangeHRM.calendar.hook(elements[x]);
	}
}

/**
 * Parse the date string.
 *
 */
YAHOO.OrangeHRM.calendar.parseDate = function (strDate) {
	format = YAHOO.OrangeHRM.calendar.format;

	if (YAHOO.OrangeHRM.calendar.formatHint.format == strDate) {
		return false;
	}

	yearVal = '';
	monthVal = '';
	dateVal = '';

	for (i=0; i<format.length; i++) {

		ch = format.charAt(i);
		sCh = strDate.charAt(i);

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

	integerFormat = /[0-9]+/;

	if (!integerFormat.test(dateVal) || !integerFormat.test(monthVal) || !integerFormat.test(yearVal)) {
		return false;
	}

	if ((monthVal < 1) || (monthVal > 12) || (dateVal < 1) || (dateVal > 31)) {
		return false;
	}

	return yearVal+"-"+monthVal+"-"+dateVal;
};

/**
 * Configures the calendar to the specific element
 *
 * If the anchor is not readonly, script will attempt to format the date
 * provided as the value of the anchor.
 *
 */
YAHOO.OrangeHRM.calendar.pop = function(anchor) {

	container = 'cal1Container';

	YAHOO.OrangeHRM.calendar.cal.anchor = anchor;
	YAHOO.OrangeHRM.calendar.cal.format = YAHOO.OrangeHRM.calendar.format;

	selDate=document.getElementById(anchor).value;
	parsedDate=YAHOO.OrangeHRM.calendar.parseDate(selDate);

	if ((!document.getElementById(anchor).readOnly) && !parsedDate) {
		document.getElementById(anchor).value="";
	}

	selDate=parsedDate;

	if (selDate) {
		YAHOO.OrangeHRM.calendar.cal.select(parsedDate);

		firstDate = YAHOO.OrangeHRM.calendar.cal.getSelectedDates()[0];
		YAHOO.OrangeHRM.calendar.cal.cfg.setProperty("pagedate", (firstDate.getMonth()+1) + "-" + firstDate.getFullYear());
	} else {
		date = YAHOO.OrangeHRM.calendar.cal.getSelectedDates();
		if (date != "") {
			YAHOO.OrangeHRM.calendar.cal.deselect(date);
		}
	}

	YAHOO.OrangeHRM.calendar.cal.render();
	YAHOO.OrangeHRM.calendar.cal.show();

	domElDimensions = YAHOO.util.Dom.getXY(anchor);
	domEl = YAHOO.util.Dom.get(anchor);

	domElDimensions[1]+=25;

	YAHOO.util.Dom.setXY(container, domElDimensions);
};

/**
 * Triggered when the a date is selected. Puts the date in to the input element
 */
YAHOO.OrangeHRM.calendar.selected = function () {
	date = this.getSelectedDates();
	document.getElementById(this.anchor).value=formatDate(date[0], this.format);

	//YAHOO.OrangeHRM.calendar.formatHint.hide.call(document.getElementById(this.anchor));

	this.hide();
	YAHOO.OrangeHRM.calendar.cal.selectedEvent.fire();
};

YAHOO.OrangeHRM.container.init = function () {
	YAHOO.OrangeHRM.container.wait = new YAHOO.widget.Panel("wait",
																{ width:"240px",
																  fixedcenter:true,
																  close:false,
																  draggable:false,
																  modal:true,
																  visible:false
																}
															);

	YAHOO.OrangeHRM.container.wait.setHeader("Loading, please wait...");
	YAHOO.OrangeHRM.container.wait.setBody("<img src=\"../../themes/beyondT/pictures/ajax-loader.gif\"/>");
	YAHOO.OrangeHRM.container.wait.render(document.body);

	// Show the Panel
	YAHOO.OrangeHRM.container.wait.show();
}

YAHOO.OrangeHRM.calendar.hook = function (button) {
	anchor = YAHOO.util.Dom.getPreviousSibling(button);
	YAHOO.util.Event.addListener(button, "click", YAHOO.OrangeHRM.calendar.selectDate, anchor, true);

	if (!anchor.readonly) {
		YAHOO.util.Event.addListener(anchor, "focus", YAHOO.OrangeHRM.calendar.formatHint.hide, anchor, true);
		YAHOO.util.Event.addListener(anchor, "blur", YAHOO.OrangeHRM.calendar.formatHint.show, anchor, true);
		//YAHOO.OrangeHRM.calendar.cal.selectedEvent.subscribe(YAHOO.OrangeHRM.calendar.formatHint.hide, anchor, true);

		YAHOO.OrangeHRM.calendar.formatHint.show.call(anchor);
	}
}

YAHOO.OrangeHRM.calendar.selectDate = function () {
	YAHOO.OrangeHRM.calendar.pop(this.id);
}

/**
 * Show the format hint
 *
 */
YAHOO.OrangeHRM.calendar.formatHint.hide = function () {
	if (this.value == YAHOO.OrangeHRM.calendar.formatHint.format) {
		this.value='';
	}
}

/**
 * Hide the format hint
 *
 */
YAHOO.OrangeHRM.calendar.formatHint.show = function () {
	if (this.value == '') {
		this.value=YAHOO.OrangeHRM.calendar.formatHint.format;
	}

	setTimeout("YAHOO.OrangeHRM.calendar.cal.hide.call(YAHOO.OrangeHRM.calendar.cal)",250);
}

/**
 * After the page has loaded the calendar is initalized
 */
YAHOO.util.Event.addListener(window, "load", YAHOO.OrangeHRM.calendar.init);
