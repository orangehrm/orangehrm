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
YAHOO.namespace("OrangeHRM.container");

/**
 * Adds the calendar to the dom
 */
YAHOO.OrangeHRM.calendar.init = function () {
	id="cal1", container="cal1Container";
	YAHOO.OrangeHRM.calendar.cal = new YAHOO.widget.Calendar(id, container, {START_WEEKDAY:1,
																			 DATE_FIELD_DELIMITER: "-",
																			 DATE_RANGE_DELIMITER: " ",
																			 MDY_DAY_POSITION: 3,
																			 MDY_MONTH_POSITION: 2,
																			 MDY_YEAR_POSITION: 1,
																			 close: true});

	YAHOO.OrangeHRM.calendar.cal.selectEvent.subscribe(YAHOO.OrangeHRM.calendar.selected, YAHOO.OrangeHRM.calendar.cal, true);

	YAHOO.OrangeHRM.calendar.cal.hide();
	YAHOO.OrangeHRM.container.wait.hide();
};

/**
 * Parse the date string.
 *
 * Allowed date delimiters - or /
 * Allowed formats with preference
 * yyyy-MM-dd
 * MM-dd-yyyy
 * dd-MM-yyyy
 *
 */
YAHOO.OrangeHRM.calendar.parseDate = function (strDate) {
	if ((strDate == "") || (strDate == "0000-00-00")) {
		return false;
	}

	dateElements = strDate.split("-");
	if (dateElements.length != 3) {
		dateElements = strDate.split("/");
	}

	if (dateElements.length != 3) {
		return false;
	}

	yearFormat = /^([0-9]{4})$/;
	monthFormat = /^[0-1]{0,1}[0-9]{1}$/;
	dateFormat = /^[0-3]{0,1}[0-9]{1}$/;

	yearSegment = false;
	monthSegment = false;
	dateSegment = false;

	if (yearFormat.test(dateElements[0])) {
		yearSegment = 0;
	} else if (yearFormat.test(dateElements[2])) {
		yearSegment = 2;
	} else {
		alert("Y")
		return false;
	}

	if (yearSegment == 2) {
		if (monthFormat.test(dateElements[0])) {
			monthSegment = 0;
		} else if (monthFormat.test(dateElements[1])) {
			monthSegment = 1;
		} else {
			return false;
		}
	} else if ((yearSegment == 0) && monthFormat.test(dateElements[1])) {
		monthSegment = 1;
	} else {
		alert("M")
		return false;
	}

	if ((yearSegment == 0) && dateFormat.test(dateElements[2])) {
		dateSegment = 2;
	} else if (yearSegment == 2) {
		if (monthSegment == 0) {
			if (dateFormat.test(dateElements[1])) {
				dateSegment = 1;
			} else if (dateFormat.test(dateElements[0])) {
				dateSegment = 0;
				monthSegment = 1;
			} else {
				return false;
			}
		} else if (dateFormat.test(dateElements[0])) {
			dateSegment = 0;
		} else {
			return false;
		}
	} else {
		alert(yearSegment);
		alert("D")
		return false;
	}

	return dateElements[yearSegment]+"-"+dateElements[monthSegment]+"-"+dateElements[dateSegment];
};

/**
 * Configures the calendar to the specific element
 *
 * If the anchor is not readonly, script will attempt to format the date
 * provided as the value of the anchor.
 *
 */
YAHOO.OrangeHRM.calendar.pop = function(anchor, container, format) {

	YAHOO.OrangeHRM.calendar.cal.format = format;
	YAHOO.OrangeHRM.calendar.cal.anchor = anchor;

	selDate=document.getElementById(anchor).value;
	parsedDate=YAHOO.OrangeHRM.calendar.parseDate(selDate);

	if ((!document.getElementById(anchor).readOnly) && parsedDate) {
		document.getElementById(anchor).value=parsedDate;
	} else if (!document.getElementById(anchor).readOnly) {
		document.getElementById(anchor).value="";
	}

	selDate=parsedDate;

	if (selDate) {
		YAHOO.OrangeHRM.calendar.cal.select(document.getElementById(anchor).value);

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

	this.hide();
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

/**
 * After the page has loaded the calendar is initalized
 */
YAHOO.util.Event.addListener(window, "load", YAHOO.OrangeHRM.calendar.init);
