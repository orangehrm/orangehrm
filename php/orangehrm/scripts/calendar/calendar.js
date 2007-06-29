YAHOO.namespace("OrangeHRM.calendar");

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
};

YAHOO.OrangeHRM.calendar.pop = function(anchor, container, format) {
	YAHOO.OrangeHRM.calendar.cal.format = format;
	YAHOO.OrangeHRM.calendar.cal.anchor = anchor;

	selDate=document.getElementById(anchor).value;
	if (selDate && (selDate != "")) {
		YAHOO.OrangeHRM.calendar.cal.select(document.getElementById(anchor).value);

		firstDate = YAHOO.OrangeHRM.calendar.cal.getSelectedDates()[0];
		YAHOO.OrangeHRM.calendar.cal.cfg.setProperty("pagedate", (firstDate.getMonth()+1) + "-" + firstDate.getFullYear());
	} else {
		date = YAHOO.OrangeHRM.calendar.cal.getSelectedDates();
		YAHOO.OrangeHRM.calendar.cal.deselect(date);
	}

	YAHOO.OrangeHRM.calendar.cal.render();
	YAHOO.OrangeHRM.calendar.cal.show();

	domElDimensions = YAHOO.util.Dom.getXY(anchor);
	domEl = YAHOO.util.Dom.get(anchor);

	domElDimensions[1] += 27;

	YAHOO.util.Dom.setXY(container, domElDimensions);
};

YAHOO.OrangeHRM.calendar.selected = function () {
	date = this.getSelectedDates();
	document.getElementById(this.anchor).value=formatDate(date[0], this.format);

	this.hide();
};

YAHOO.util.Event.addListener(window, "load", YAHOO.OrangeHRM.calendar.init);