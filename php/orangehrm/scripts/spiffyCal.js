//
// Bazillyo's Spiffy DHTML Popup Calendar Control - beta version 2.0 Release Candidate 1
// ©2001 S. Ousta email me bazillyo@yahoo.com or 
// see website for copyright information http://www.geocities.com/bazillyo/spiffy/calendar/index.htm
// Permission granted to SimplytheBest.net to feature the script in the 
// DHTML script collection at http://simplythebest.net/info/dhtml_scripts.html
//
// GLOBAL variables

var scIE=((navigator.appName == "Microsoft Internet Explorer") || ((navigator.appName == "Netscape") && (parseInt(navigator.appVersion)==5)));
var scNN6=((navigator.appName == "Netscape") && (parseInt(navigator.appVersion)==5));
var scNN=((navigator.appName == "Netscape")&&(document.layers));

var img_Del=new Image();
var img_Close=new Image();

img_Del.src="images/btn_del_small.gif";
img_Close.src="images/btn_close_small.gif";

var scBTNMODE_DEFAULT=0;
var scBTNMODE_CUSTOMBLUE=1;
var scBTNMODE_CALBTN=2;

var focusHack;

/*================================================================================
 * Calendar Manager Object
 * 
 * 	the functions:
 * 		isDate(), formatDate(), _isInteger(), _getInt(), and getDateFromFormat()
 * 	are based on ones courtesy of Matt Kruse (mkruse@netexpress.net) http://www.mattkruse.com/javascript/
 * 	with some modifications by myself and Michael Brydon
 *
 */
 
function spiffyCalManager() {
	
	this.showHelpAlerts = false;
	this.defaultDateFormat='dd-MM-yyyy';
	this.lastSelectedDate=new Date();
	this.calendars=new Array();	
	this.matchedFormat="";
	this.DefBtnImgPath='images/';

	// methods	 ----------------------------------------------------------------------
	this.getCount= new Function("return this.calendars.length;");
		
	function addCalendar(objWhatCal) {
		var intIndex = this.calendars.length;
		this.calendars[intIndex] = objWhatCal;
	}
	this.addCalendar=addCalendar;	
	
		
	function hideAllCalendars(objExceptThisOne) {
		var i=0;
		for (i=0;i<this.calendars.length;i++) {
			if (objExceptThisOne!=this.calendars[i]) {
				this.calendars[i].hide();
			}
		}

	}
	this.hideAllCalendars=hideAllCalendars;
	
	function swapImg(objWhatCal, strToWhat, blnStick) {
		if (document.images) {
			// this makes it so that the button sticks down when the cal is visible
			if ((!(objWhatCal.visible) || (blnStick))&& (objWhatCal.enabled)) {
				document.images[objWhatCal.btnName].src = eval(objWhatCal.varName+strToWhat + ".src");
			}
		}
		window.status=' ';
	//	return true;	
	}
	this.swapImg=swapImg;
	

	// DATE FUNCTIONS -----------------------


	this.AllowedFormats = new Array('d.M',
'd-M',
'd/M',
'd.MMM',
'd-MMM',
'd/MMM',
'd.M.yy',
'd-M-yy',
'd/M/yy',
'd.M.yyyy',
'd-M-yyyy',
'd/M/yyyy',
'd.MM.yyyy',
'd-MM-yyyy',
'd/MM/yyyy',
'd.MMM.yy',
'd-MMM-yy',
'd/MMM/yy',
'd.MMM.yyyy',
'd-MMM-yyyy',
'd/MMM/yyyy',
'dd.MM.yy',
'dd-MM-yy',
'dd/MM/yy',
'dd.MM.yyyy',
'dd-MM-yyyy',
'dd/MM/yyyy',
'dd.MMM.yy',
'dd-MMM-yy',
'dd/MMM/yy',
'dd.MMM.yyyy',
'dd-MMM-yyyy',
'dd/MMM/yyyy',
'M.d',
'M-d',
'M/d',
'MMM.d',
'MMM-d',
'MMM/d',
'M.d.yy',
'M-d-yy',
'M/d/yy',
'MM.d.yyyy',
'MM-d-yyyy',
'MM/d/yyyy',
'MMM.d.yy',
'MMM-d-yy',
'MMM/d/yy',
'MMM.d.yyyy',
'MMM-d-yyyy',
'MMM/d/yyyy',
'MM.dd.yy',
'MM-dd-yy',
'MM/dd/yy',
'MM.dd.yyyy',
'MM-dd-yyyy',
'MM/dd/yyyy',
'MMM.dd.yy',
'MMM-dd-yy',
'MMM/dd/yy',
'MMM.dd.yyyy',
'MMM-dd-yyyy',
'MMM/dd/yyyy');
	var MONTH_NAMES = new Array('January','February','March','April','May','June','July','August','September','October','November','December','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');

	this.lastBoxValidated=null;


	function validateDate(eInput, bRequired, dStartDate, dEndDate){
		var i = 0; var strTemp=''; var formatMatchCount=0; var firstMatchAt=0;var secondMatchAt=0;
		var bOK = false; var bIsEmpty=false; 
		this.lastBoxValidated=eInput;
		this.matchedFormat="";
		bIsEmpty=(eInput.value=='' || eInput.value==null);
		if (!(bRequired && bIsEmpty)) {
			for(i=0;i<this.AllowedFormats.length;i++){
				if (isDate(eInput.value, this.AllowedFormats[i])==true){
					bOK = true;
					formatMatchCount+=1;
					if (formatMatchCount==1) {firstMatchAt=i;}
					if (formatMatchCount>1) {secondMatchAt=i; break;}
				}
			}
		}
		
		if (formatMatchCount>1) {

			if (this.showHelpAlerts) {		

				var date1=getDateFromFormat(eInput.value,this.AllowedFormats[firstMatchAt]);
				var choice1 = MONTH_NAMES[date1.getMonth()]+'-'+date1.getDate()+'-'+date1.getFullYear(); 
				var date2=getDateFromFormat(eInput.value,this.AllowedFormats[secondMatchAt]);
				var choice2 = MONTH_NAMES[date2.getMonth()]+'-'+date2.getDate()+'-'+date2.getFullYear(); 

				if (date1.getTime()!=date2.getTime()) {
					var Msg='You have entered an ambiguous date.\n\n Click OK for:\n'+ choice1 +'\n\nor Click Cancel for:\n'+choice2;	
					if (confirm(Msg)) {
						bOK=true;
					}
					else {
						firstMatchAt=secondMatchAt;
						bOK=true;
						//return false;
					}
					eInput.focus();
					eInput.select();
				}
			}
			else {
				// continue and take first match in list				
				bOK=true;
			}
		}
		
		if (bOK==true) {
			eInput.className = "cal-TextBox";
			//Check for Start/End Dates

			if (dStartDate!=null) {
				//Required dd-MMM-yyyy	
				var dStart = getDateFromFormat(dStartDate,"dd-MMM-yyyy");
				var dThis = getDateFromFormat(eInput.value,this.AllowedFormats[i]);
				if (dStart>dThis){
					eInput.className = "cal-TextBoxInvalid";
					if (this.showHelpAlerts) { alert('Please enter a date no earlier than ' + dStartDate + '.');}
					eInput.focus();
					eInput.select();				
					return false;
				}
			}
			if (dEndDate!=null) {
				//Required dd-MMM-yyyy	
				var dEnd = getDateFromFormat(dEndDate,"dd-MMM-yyyy");
				var dThis = getDateFromFormat(eInput.value,this.AllowedFormats[i]);
				if (dEnd<dThis) {
					eInput.className = "cal-TextBoxInvalid";
					if (this.showHelpAlerts) { alert('Please enter a date no later than ' + dEndDate + '.');}
					eInput.focus();
					eInput.select();
					return false;
				}
			}
			this.matchedFormat=this.AllowedFormats[firstMatchAt];
			
			this.lastBoxValidated = null;
		}
		else { 	
			
			if (bRequired && bIsEmpty) {
				eInput.className = "cal-TextBoxInvalid";
				if (this.showHelpAlerts) {
					alert('This date field is required.\n\nPlease enter a valid date before proceeding.');
				}
			}
			else {
				if (!bRequired && bIsEmpty) {
					eInput.className = "cal-TextBox";
				}
				else { 
					eInput.className = "cal-TextBoxInvalid";
					if (this.showHelpAlerts) {
						for(i=0;i<this.AllowedFormats.length;i++){
							strTemp+=this.AllowedFormats[i]+'\t';
						}
						alert('Please enter a valid date.\n\nExample 01-Jan-2002\n\nValid formats are:\n\n'+strTemp);
					}
				}
			}
			eInput.focus();
			eInput.select();
			focusHack=eInput;
			
			setTimeout('focusHack.focus();focusHack.select();');
			return false;
		}
	}
	this.validateDate=validateDate;
	

	function formatDate(eInput, strFormat) {
		//Always called directly following validateDate  - put validate in onchange and format in onblur.
		if(this.matchedFormat!="") {
			var d = getDateFromFormat(eInput.value,this.matchedFormat);
			if(d!=0){
				eInput.value = scFormatDate(d, strFormat);
			}
		}
	}
	this.formatDate=formatDate;

	function isDate(val,format) {
		var date = getDateFromFormat(val,format);
		if (date == 0) { return false; }
		return true;
	}
	this.isDate=isDate;
	

	function scFormatDate(date,format) {
		format = format+"";
		var result = "";
		var i_format = 0;
		var c = "";
		var token = "";
		var y = date.getFullYear()+"";
		var M = date.getMonth()+1;
		var d = date.getDate();
		var h = date.getHours();
		var m = date.getMinutes();
		var s = date.getSeconds();
		var yyyy,yy,MMM,MM,dd;
		// Convert real date parts into formatted versions
		// Year
		if (y.length < 4) {
			y = y-0+1900;
			}
		y = ""+y;
		yyyy = y;
		yy = y.substring(2,4);
		// Month
		if (M < 10) { MM = "0"+M; }
			else { MM = M; }
		MMM = MONTH_NAMES[M-1+12];
		// Date
		if (d < 10) { dd = "0"+d; }
			else { dd = d; }
		// Now put them all into an object!
		var value = new Object();
		value["yyyy"] = yyyy;
		value["yy"] = yy;
		value["y"] = y;
		value["MMM"] = MMM;
		value["MM"] = MM;
		value["M"] = M;
		value["dd"] = dd;
		value["d"] = d;

		while (i_format < format.length) {
			// Get next token from format string
			c = format.charAt(i_format);
			token = "";
			while ((format.charAt(i_format) == c) && (i_format < format.length)) {
				token += format.charAt(i_format);
				i_format++;
				}
			if (value[token] != null) {
				result = result + value[token];
				}
			else {
				result = result + token;
				}
			}
		return result;
	}
	this.scFormatDate=scFormatDate;

	function _isInteger(val) {
		var digits = "1234567890";
		for (var i=0; i < val.length; i++) {
			if (digits.indexOf(val.charAt(i)) == -1) { return false; }
			}
		return true;
	}

	function _getInt(str,i,minlength,maxlength) {
		for (x=maxlength; x>=minlength; x--) {
			var token = str.substring(i,i+x);
			if (_isInteger(token)) { 
				return token;
				}
			}
		return null;
	}

	function getDateFromFormat(val,format) {
		val = val+"";
		format = format+"";
		var i_val = 0;
		var i_format = 0;
		var c = "";
		var token = "";
		var token2= "";
		var x,y;
		var year  = 0;
		var month = 0;
		var date  = 0;
		var bYearProvided = false;
		while (i_format < format.length) {
			// Get next token from format string
			c = format.charAt(i_format);
			token = "";
			
			while ((format.charAt(i_format) == c) && (i_format < format.length)) {
				token += format.charAt(i_format);
				i_format++;
			}
			
			// Extract contents of value based on format token
			if (token=="yyyy" || token=="yy" || token=="y") {
				if (token=="yyyy") { x=4;y=4; }// 4-digit year
				if (token=="yy")   { x=2;y=2; }// 2-digit year
				if (token=="y")    { x=2;y=4; }// 2-or-4-digit year
				year = _getInt(val,i_val,x,y);
				bYearProvided = true;
				if (year == null) {
					return 0; 
					//Default to current year 
				}		
				if (year.length != token.length){
					return 0;
				}

				i_val += year.length;
			}
			else if (token=="MMM") { // Month name
				month = 0;
				for (var i=0; i<MONTH_NAMES.length; i++) {
					var month_name = MONTH_NAMES[i];
					if (val.substring(i_val,i_val+month_name.length).toLowerCase() == month_name.toLowerCase()) {
						month = i+1;
						if (month>12) { month -= 12; }
						i_val += month_name.length;
						break;
					}
				}
				
				if (month == 0) { return 0; }
				if ((month < 1) || (month>12)) {
					return 0
				}
			}
			else if (token=="MM" || token=="M") {
				x=token.length; y=2;
				month = _getInt(val,i_val,x,y);
				if (month == null) { return 0; }
				if ((month < 1) || (month > 12)) { return 0; }
				i_val += month.length;
			}
			else if (token=="dd" || token=="d") {
				x=token.length; y=2;
				date = _getInt(val,i_val,x,y);
				if (date == null) { return 0; }
				if ((date < 1) || (date>31)) { return 0; }
				i_val += date.length;
			}
			else {
				if (val.substring(i_val,i_val+token.length) != token) {
					return 0;
				}
				else {
					i_val += token.length;
				}
			}
		}
		// If there are any trailing characters left in the value, it doesn't match
		if (i_val != val.length) {
			return 0;
		}
		// Is date valid for month?

		if (month == 2) {
			// Check for leap year
			if ( ( (year%4 == 0)&&(year%100 != 0) ) || (year%400 == 0) ) { // leap year
				if (date > 29){ return false; }
			}
			else {
				if (date > 28) { return false; }
			}
		}
		if ((month==4)||(month==6)||(month==9)||(month==11)) {
			if (date > 30) { return false; }
		}

		//JS dates uses 0 based months.
		month = month - 1;

		if (bYearProvided==false) {
			//Default to current
			var dCurrent = new Date();
			year = dCurrent.getFullYear();
		}

		var lYear = parseInt(year);
		if (lYear<=20) {
			year = 2000 + lYear;
		}
		else if (lYear >=21 && lYear<=99) {
			year = 1900 + lYear;	
		}

		var newdate = new Date(year,month,date,0,0,0);

		return newdate;
	}
	this.getDateFromFormat=getDateFromFormat;


}



var calMgr = new spiffyCalManager();



//================================================================================
// Calendar Object

function ctlSpiffyCalendarBox(strVarName, strFormName, strTextBoxName, strBtnName, strDefaultValue, intBtnMode) {

	var msNames     = new makeArray0('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
	var msDays      = new makeArray0(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	var msDOW       = new makeArray0('S','M','T','W','T','F','S');


	var blnInConstructor=true;
	var img_DateBtn_UP=new Image();
	var img_DateBtn_OVER=new Image();
	var img_DateBtn_DOWN=new Image();
	var img_DateBtn_DISABLED=new Image();

	var strBtnW;
	var strBtnH;
	var strBtnImg;
	
	var dteToday=new Date;
	var dteCur=new Date;
	
	var dteMin=new Date;
	var dteMax=new Date;
	
	var scX=4; // default where to display calendar
	var scY=4;
	
	// Defaults
	var strDefDateFmt='yyyy-MM-dd';
	
	var intDefBtnMode=0;
	var strDefBtnImgPath=calMgr.DefBtnImgPath;
	/* PROPERTIES =============================================================
	 *
	 */
	// Generic Properties
	this.varName=strVarName;
	this.enabled=true;
	this.readonly=false;
	this.visible=false;
	this.displayLeft=false;
	this.displayTop=false;
	// Name Properties
	this.formName=strFormName;
	this.textBoxName=strTextBoxName;
	this.btnName=strBtnName;
	this.required=false;
	
	this.imgUp=img_DateBtn_UP;
	this.imgOver=img_DateBtn_OVER;
	this.imgDown=img_DateBtn_DOWN;
	this.imgDisabled=img_DateBtn_DISABLED;
	
	// look
	this.textBoxWidth=160;
	this.textBoxHeight=20;
	this.btnImgWidth=strBtnW;
	this.btnImgHeight=strBtnH;
	if ((intBtnMode==null)||(intBtnMode<0 && intBtnMode>2)) {
		intBtnMode=intDefBtnMode
	}
	switch (intBtnMode) {
		case 0 :
			strBtnImg=strDefBtnImgPath+'btn_date_up.gif';
			img_DateBtn_UP.src=strDefBtnImgPath+'btn_date_up.gif';
			img_DateBtn_OVER.src=strDefBtnImgPath+'btn_date_over.gif';
			img_DateBtn_DOWN.src=strDefBtnImgPath+'btn_date_down.gif';
			img_DateBtn_DISABLED.src=strDefBtnImgPath+'btn_date_disabled.gif';
			strBtnW = '18';
			strBtnH = '20';
			break;
		case 1 :
			strBtnImg=strDefBtnImgPath+'btn_date1_up.gif';
			img_DateBtn_UP.src=strDefBtnImgPath+'btn_date1_up.gif';			
			img_DateBtn_OVER.src=strDefBtnImgPath+'btn_date1_over.gif';			
			img_DateBtn_DOWN.src=strDefBtnImgPath+'btn_date1_down.gif';
			img_DateBtn_DISABLED.src=strDefBtnImgPath+'btn_date1_disabled.gif';
			strBtnW = '22';
			strBtnH = '17';
			break;
		case 2 :
			strBtnImg=strDefBtnImgPath+'btn_date2_up.gif';
			img_DateBtn_UP.src=strDefBtnImgPath+'btn_date2_up.gif';			
			img_DateBtn_OVER.src=strDefBtnImgPath+'btn_date2_over.gif';			
			img_DateBtn_DOWN.src=strDefBtnImgPath+'btn_date2_down.gif';
			img_DateBtn_DISABLED.src=strDefBtnImgPath+'btn_date2_disabled.gif';
			strBtnW = '34';
			strBtnH = '21';
			break;
	}	
	// Date Properties
	this.dateFormat=strDefDateFmt;
	this.useDateRange=false;
	
	this.minDate=new Date;
	this.maxDate=new Date(dteToday.getFullYear()+1, dteToday.getMonth(), dteToday.getDate());

	this.minDay = function() {
		return this.minDate.getDate();
	}
	this.minMonth = function() {
		return this.minDate.getMonth();
	}
	this.minYear = function() {
		return this.minDate.getFullYear();
	}
	
	this.maxDay = function() {
		return this.maxDate.getDate();
	}
	this.maxMonth = function() {
		return this.maxDate.getMonth();
	}
	this.maxYear = function() {
		return this.maxYear.getFullYear();
	}
		

	function setMinDate(intYear, intMonth, intDay) {
		this.minDate = new Date(intYear, intMonth-1, intDay);
	}
	this.setMinDate=setMinDate;


	function setMaxDate(intYear, intMonth, intDay) {
		this.maxDate = new Date(intYear, intMonth-1, intDay);
	}
	this.setMaxDate=setMaxDate;

	this.minYearChoice=dteToday.getFullYear()-10;	
	this.maxYearChoice=dteToday.getFullYear()+10;
	this.textBox= function() {
		if (!blnInConstructor) {	
			return eval('document.'+this.formName+'.'+this.textBoxName);
		}
	}
	
	this.getSelectedDate = function () {
		var strTempVal=''; var objEle;
		if ((typeof this.formName !='undefined') && (typeof this.textBoxName!='undefined')) {		
			objEle=eval('document.'+this.formName+'.'+this.textBoxName);
			if (objEle && !blnInConstructor) {
				strTempVal=eval('document.'+this.formName+'.'+this.textBoxName+'.value');
			}
			else {
				strTempVal=strDefaultValue;
			}
		}
		else {
			strTempVal=strDefaultValue;
		}
		return strTempVal;
	}

	function setSelectedDate(strWhat) {
		var strTempVal=''; var objEle;
		eval('document.'+this.formName+'.'+this.textBoxName).value=strWhat;
		
		if (!calMgr.isDate(quote(strWhat),quote(this.dateFormat))) {
			eval('document.'+this.formName+'.'+this.textBoxName).className = "cal-TextBoxInvalid";
		}
		else {
			eval('document.'+this.formName+'.'+this.textBoxName).className = "cal-TextBox";
		}
	}
	this.setSelectedDate=setSelectedDate;

	
	function disable() {
		this.hide();
		calMgr.swapImg(this,'.imgDisabled',false);    
		this.enabled=false;
		eval('document.'+this.formName+'.'+this.textBoxName).disabled=true;
        eval('document.'+this.formName+'.'+this.textBoxName).className = "cal-TextBoxDisabled";
		if (scNN) {
			eval('document.'+this.formName+'.'+this.textBoxName).onFocus= function() {this.blur();};
		}       
	}
	this.disable=disable;
	
	function enable() {
		this.enabled=true;
		calMgr.swapImg(this,'.imgUp',false);    
		eval('document.'+this.formName+'.'+this.textBoxName).disabled=false;
        eval('document.'+this.formName+'.'+this.textBoxName).className = "cal-TextBox";
		if (scNN) {
			eval('document.'+this.formName+'.'+this.textBoxName).onFocus= null;
		}
		
		if (!calMgr.isDate(quote(this.getSelectedDate()),quote(this.dateFormat))) {
			eval('document.'+this.formName+'.'+this.textBoxName).className = "cal-TextBoxInvalid";
		}
	}
	this.enable=enable;
	

	
	// behavior Properties
	this.JStoRunOnSelect='';
	this.JStoRunOnClear='';
	this.JStoRunOnCancel='';
	this.hideCombos=true;
	
	
	/* METHODS ===============================================================
	 *
	 */
	
	function makeCalendar(intWhatMonth,intWhatYear,bViewOnly) {
		if (bViewOnly) {intWhatMonth-=1;}
		var strOutput = '';
		var intStartMonth=intWhatMonth;
		var intStartYear=intWhatYear;
		var intLoop;
		var strTemp='';
		var strDateColWidth;	

		dteCur.setMonth(intWhatMonth);
		dteCur.setFullYear(intWhatYear);
		dteCur.setDate(dteToday.getDate());
		dteCur.setHours(0);dteCur.setMinutes(0);dteCur.setSeconds(0);dteCur.setMilliseconds(0);
		if (!(bViewOnly)) {
			strTemp='<form name="spiffyCal"';
		}
		// special case for form not to be inside table in Netscape 6
		if (scNN6) {
			strOutput += strTemp +'<table width="185" border="3" class="cal-Table" cellspacing="0" cellpadding="0"><tr>';
		}
		else {
			strOutput += '<table width="185" border="3" class="cal-Table" cellspacing="0" cellpadding="0">'+strTemp+'<tr>';
		}

		if (!(bViewOnly)) {
			strOutput += '<td class="cal-HeadCell" align="center" width="100%"><a href="javascript:'+this.varName+'.clearDay();"><img name="calbtn1" src="'+strDefBtnImgPath+'btn_del_small.gif" border="0" width="12" height="10"></a>&nbsp;&nbsp;<a href="javascript:'+this.varName+'.scrollMonth(-1);" class="cal-DayLink">&lt;</a>&nbsp;<SELECT class="cal-ComboBox" NAME="cboMonth" onChange="'+this.varName+'.changeMonth();">';


			for (intLoop=0; intLoop<12; intLoop++) {
				if (intLoop == intWhatMonth) strOutput += '<OPTION VALUE="' + intLoop + '" SELECTED>' + msNames[intLoop] + '<\/OPTION>';
				else  strOutput += '<OPTION VALUE="' + intLoop + '">' + msNames[intLoop] + '<\/OPTION>';
			}


			strOutput += '<\/SELECT><SELECT class="cal-ComboBox" NAME="cboYear" onChange="'+this.varName+'.changeYear();">';

			for (intLoop=this.minYearChoice; intLoop<this.maxYearChoice; intLoop++) {
				if (intLoop == intWhatYear) strOutput += '<OPTION VALUE="' + intLoop + '" SELECTED>' + intLoop + '<\/OPTION>';
				else strOutput += '<OPTION VALUE="' + intLoop + '">' + intLoop + '<\/OPTION>';
			}

			strOutput += '<\/SELECT>&nbsp;<a href="javascript:'+this.varName+'.scrollMonth(1);" class="cal-DayLink">&gt;</a>&nbsp;&nbsp;<a href="javascript:'+this.varName+'.hide();"><img name="calbtn2" src="'+strDefBtnImgPath+'btn_close_small.gif" border="0" width="12" height="10"></a><\/td><\/tr><tr><td width="100%" align="center">';
		}
		else {
			strOutput += '<td class="cal-HeadCell" align="center" width="100%">'+msNames[intWhatMonth]+'-'+intWhatYear+'<\/td><\/tr><tr><td width="100%" align="center">';		
		}


		firstDay = new Date(intWhatYear,intWhatMonth,1);
		startDay = firstDay.getDay();

		if (((intWhatYear % 4 == 0) && (intWhatYear % 100 != 0)) || (intWhatYear % 400 == 0))
			msDays[1] = 29;
		else
			msDays[1] = 28;

		strOutput += '<table width="185" cellspacing="1" cellpadding="2" border="0"><tr>';

		for (intLoop=0; intLoop<7; intLoop++) {
			if (intLoop==0 || intLoop==6) {
				strDateColWidth="15%"
			}
			else
			{
				strDateColWidth="14%"
			}
			strOutput += '<td class="cal-HeadCell" width="' + strDateColWidth + '" align="center" valign="middle">'+ msDOW[intLoop] +'<\/td>';
		}

		strOutput += '<\/tr><tr>';

		var intColumn = 0;
		var intLastMonth = intWhatMonth - 1;
		var intLastYear = intWhatYear;
		
		if (intLastMonth == -1) { intLastMonth = 11; intLastYear=intLastYear-1;}

		for (intLoop=0; intLoop<startDay; intLoop++, intColumn++) {
			strOutput += this.getDayLink(true,(msDays[intLastMonth]-startDay+intLoop+1),intLastMonth,intLastYear,bViewOnly);
		}

		for (intLoop=1; intLoop<=msDays[intWhatMonth]; intLoop++, intColumn++) {
			strOutput += this.getDayLink(false,intLoop,intWhatMonth,intWhatYear,bViewOnly);
			if (intColumn == 6) {
				strOutput += '<\/tr><tr>';
				intColumn = -1;
			}
		}

		var intNextMonth = intWhatMonth+1;
		var intNextYear = intWhatYear;
		
		if (intNextMonth==12) { intNextMonth=0; intNextYear=intNextYear+1;}

		if (intColumn > 0) {
			for (intLoop=1; intColumn<7; intLoop++, intColumn++) {
				strOutput +=  this.getDayLink(true,intLoop,intNextMonth,intNextYear,bViewOnly);
			}
			strOutput += '<\/tr><\/table><\/td><\/tr>';
		}
		else {
			strOutput = strOutput.substr(0,strOutput.length-4); // remove the <tr> from the end if there's no last row
			strOutput += '<\/table><\/td><\/tr>';
		}

		if (scNN6) {
			strOutput += '<\/table><\/form>';
		}
		else {
			strOutput += '<\/form><\/table>';
		}
		dteCur.setDate(1);
		dteCur.setHours(0);dteCur.setMinutes(0);dteCur.setSeconds(0);dteCur.setMilliseconds(0);

		dteCur.setMonth(intStartMonth);
		dteCur.setFullYear(intStartYear);
		return strOutput;
	}	
	this.makeCalendar=makeCalendar;
	

	// writeControl -------------------------------------
	//
	function writeControl() {
		var strHold='';
		var strTemp='';
		var strTempMinDate='';
		var strTempMaxDate='';
		
		// specify whether you can type in the date box and validate them as well
		// or whether you must use the calendar only to select a date
		if (this.readonly) {
			strTemp=' onFocus="this.blur();" readonly ';
		}

		if (!(this.useDateRange)) { 
			strTemp+=' onChange="calMgr.validateDate(document.'+this.formName+'.'+this.textBoxName+','+this.varName+'.required);" onBlur="calMgr.formatDate(document.'+this.formName+'.'+this.textBoxName+','+this.varName+'.dateFormat);" ';
		}
		else {
			strTempMinDate=this.minDate.getDate()+'-'+msNames[this.minDate.getMonth()]+'-'+this.minDate.getFullYear();
			strTempMaxDate=this.maxDate.getDate()+'-'+msNames[this.maxDate.getMonth()]+'-'+this.maxDate.getFullYear();
			strTemp+=' onChange="calMgr.validateDate('+'document.'+this.formName+'.'+this.textBoxName+','+this.varName+'.required,'+quote(strTempMinDate)+','+quote(strTempMaxDate)+');" onBlur="calMgr.formatDate(document.'+this.formName+'.'+this.textBoxName+','+this.varName+'.dateFormat);" ';
		}

		strHold='<input class="cal-TextBox" type="text" name="' + this.textBoxName + '"' + strTemp + 'size="12" value="' + this.getSelectedDate() + '">';
		if (!scIE) {
			strTemp=' href="javascript:calClick();return false;" ';
		}
		else {
			strTemp='';
		}
		strHold+='<a class="so-BtnLink"'+strTemp;

		strHold+=' onmouseover="calMgr.swapImg(' + this.varName + ',\'.imgOver\',false);" ';

		strHold+='onmouseout="calMgr.swapImg(' + this.varName + ',\'.imgUp\',false);" ';

		strHold+='onclick="calMgr.swapImg(' + this.varName + ',\'.imgDown\',true);';

//		strHold+=this.varName+'.show();return false;">';
		strHold+=this.varName+'.show();">';

		strHold+='<img align="absmiddle" border="0" name="' + this.btnName + '" src="' + strBtnImg +'" width="'+ strBtnW +'" height="'+ strBtnH +'"></a>';
		document.write(strHold);
	}
	this.writeControl=writeControl;
	
	
	// show -------------------------------------
	//
	function show() {
		var strCurSelDate = calMgr.lastSelectedDate;

		if (!this.enabled) { return }
		calMgr.hideAllCalendars(this);						
		if (this.visible) {
			this.hide();
		}
		else {
// put these next 2 lines in when the tiny cal btns seem to randomly disappear		
 			if (document.images['calbtn1']!=null ) document.images['calbtn1'].src=img_Del.src;
 			if (document.images['calbtn2']!=null ) document.images['calbtn2'].src=img_Close.src;

			// get correct position of date btn
			if ( scIE ) {
				if (this.displayLeft) {
					scX = getOffsetLeft(document.images[this.btnName])-192+ document.images[this.btnName].width ;    
				}
				else {
					scX = getOffsetLeft(document.images[this.btnName]);    
				}
				if (this.displayTop) {
					scY = getOffsetTop(document.images[this.btnName]) -138 ;
				}
				else {
					scY = getOffsetTop(document.images[this.btnName]) + document.images[this.btnName].height + 2;
				}
			}
			else if (scNN){
				if (this.displayLeft) {
					scX = document.images[this.btnName].x - 192+  document.images[this.btnName].width; 
				}
				else {
					scX = document.images[this.btnName].x; 
				}
				if (this.displayTop) {
					scY = document.images[this.btnName].y -134;
				}
				else {
					scY = document.images[this.btnName].y + document.images[this.btnName].height + 2;
				}
			}
			// hide all combos underneath it
			if (this.hideCombos) {toggleCombos('hidden');}

			// pop calendar up to the correct month and year if there's a date there
			// otherwise pop it up using today's month and year
			if (this.getSelectedDate()==''){
				if (!(dteCur)) {
					domlay('spiffycalendar',1,scX,scY,this.makeCalendar(dteToday.getMonth(),dteToday.getFullYear()));       
				}
				else {
					domlay('spiffycalendar',1,scX,scY,this.makeCalendar(dteCur.getMonth(),dteCur.getFullYear()));
				}
			}
			else {
				if (calMgr.isDate(quote(this.getSelectedDate()),quote(this.dateFormat))) {
				    dteCur = calMgr.getDateFromFormat(quote(this.getSelectedDate()),quote(this.dateFormat));			
					dteCur.setHours(0);dteCur.setMinutes(0);dteCur.setSeconds(0);dteCur.setMilliseconds(0);

				}
				else {
					dteCur=calMgr.lastSelectedDate;
				}
				domlay('spiffycalendar',1,scX,scY,this.makeCalendar(dteCur.getMonth(),dteCur.getFullYear()));
			}

			this.visible=true;
		}
		
	}
	this.show=show;
	
		
	// hide -------------------------------------
	//
	function hide() {
	
		domlay('spiffycalendar',0,scX,scY);
		this.visible = false;
		calMgr.swapImg(this,'.imgUp',false);    
		if (this.hideCombos) {toggleCombos('visible');}
	}
	this.hide=hide;
	
	
	// clearDay -------------------------------------
	//
	function clearDay() {
		eval('document.' + this.formName + '.' + this.textBoxName + '.value = \'\'');
		this.hide();
		if (this.JStoRunOnClear!=null)
			eval(unescape(this.JStoRunOnClear)); 

		eval('document.'+this.formName+'.'+this.textBoxName).className = "cal-TextBox";
		if (this.required) {
			eval('document.'+this.formName+'.'+this.textBoxName).className = "cal-TextBoxInvalid";	
		}
	}
	this.clearDay=clearDay;
	

	// changeDay -------------------------------------
	//
	function changeDay(intWhatDay) {
		dteCur.setDate(intWhatDay);
		dteCur.setHours(0);dteCur.setMinutes(0);dteCur.setSeconds(0);dteCur.setMilliseconds(0);

		this.textBox().value=calMgr.scFormatDate(dteCur,this.dateFormat);
		this.hide();
		if (this.JStoRunOnSelect!=null)
			eval(unescape(this.JStoRunOnSelect)); 

		eval('document.'+this.formName+'.'+this.textBoxName).className = "cal-TextBox";
	
	}
	this.changeDay=changeDay;

	// scrollMonth -------------------------------------
	//
	function scrollMonth(intAmount) {
		var intMonthCheck;
		var intYearCheck;

		if (scIE) {
			intMonthCheck = document.forms["spiffyCal"].cboMonth.selectedIndex + intAmount;
		}
		else if (scNN) {
			intMonthCheck = document.spiffycalendar.document.forms["spiffyCal"].cboMonth.selectedIndex + intAmount;    
		}
		if (intMonthCheck < 0) {
			intYearCheck = dteCur.getFullYear() - 1;
			if ( intYearCheck < this.minYearChoice ) {
				intYearCheck = this.minYearChoice;
				intMonthCheck = 0;
			}
			else {
				intMonthCheck = 11;
			}
			dteCur.setFullYear(intYearCheck);
		}
		else if (intMonthCheck >11) {
			intYearCheck = dteCur.getFullYear() + 1;
			if ( intYearCheck > this.maxYearChoice-1 ) {
				intYearCheck = this.maxYearChoice-1;
				intMonthCheck = 11;
			}
			else {
				intMonthCheck = 0;
			}
			dteCur.setFullYear(intYearCheck);
		}

		if (scIE) {
			dteCur.setMonth(document.forms["spiffyCal"].cboMonth.options[intMonthCheck].value);
		}
		else if (scNN) {
			dteCur.setMonth(document.spiffycalendar.document.forms["spiffyCal"].cboMonth.options[intMonthCheck].value );
		}
		domlay('spiffycalendar',1,scX,scY,this.makeCalendar(dteCur.getMonth(),dteCur.getFullYear()));
	}
	this.scrollMonth=scrollMonth;


	// changeMonth -------------------------------------
	//
	function changeMonth() {
		if (scIE) {        
			dteCur.setMonth(document.forms["spiffyCal"].cboMonth.options[document.forms["spiffyCal"].cboMonth.selectedIndex].value);
			domlay('spiffycalendar',1,scX,scY,this.makeCalendar(dteCur.getMonth(),dteCur.getFullYear()));
		}
		else if (scNN) {
			dteCur.setMonth(document.spiffycalendar.document.forms["spiffyCal"].cboMonth.options[document.spiffycalendar.document.forms["spiffyCal"].cboMonth.selectedIndex].value);
			domlay('spiffycalendar',1,scX,scY,this.makeCalendar(dteCur.getMonth(),dteCur.getFullYear()));
		}
	}
	this.changeMonth=changeMonth;


	// changeYear -------------------------------------
	//
	function changeYear() {
		if (scIE) {
			dteCur.setFullYear(document.forms["spiffyCal"].cboYear.options[document.forms["spiffyCal"].cboYear.selectedIndex].value);
			domlay('spiffycalendar',1,scX,scY,this.makeCalendar(dteCur.getMonth(),dteCur.getFullYear()));
		}
		else if (scNN) {
			dteCur.setFullYear(document.spiffycalendar.document.forms["spiffyCal"].cboYear.options[document.spiffycalendar.document.forms["spiffyCal"].cboYear.selectedIndex].value);
			domlay('spiffycalendar',1,scX,scY,this.makeCalendar(dteCur.getMonth(),dteCur.getFullYear()));
		}
	}	
	this.changeYear=changeYear;
	
	function getDayLink(blnIsGreyDate,intLinkDay,intLinkMonth,intLinkYear,bViewOnly) {
		var templink;
		if (!(this.useDateRange)) {
			if (blnIsGreyDate) {
				templink='<td align="center" class="cal-GreyDate">' + intLinkDay + '<\/td>';
			}
			else {
				if (isDayToday(intLinkDay)) {
					if (!(bViewOnly)) {
						templink='<td align="center" class="cal-DayCell">' + '<a class="cal-TodayLink" onmouseover="self.status=\' \';return true" href="javascript:'+this.varName+'.changeDay(' + intLinkDay + ');">' + intLinkDay + '<\/a><\/td>';
					}
					else {
						templink='<td align="center" class="cal-DayCell"><span class="cal-Today">' + intLinkDay +'<\/span><\/td>';
					}
				}
				else {
					if (!(bViewOnly)) {
						templink='<td align="center" class="cal-DayCell">' + '<a class="cal-DayLink" onmouseover="self.status=\' \';return true" href="javascript:'+this.varName+'.changeDay(' + intLinkDay + ');">' + intLinkDay + '<\/a>' +'<\/td>';
					}
					else {
						templink='<td align="center" class="cal-DayCell"><span class="cal-Day">' + intLinkDay + '<\/span><\/td>';
					}
				}
			}
		}
		else {
			if (this.isDayValid(intLinkDay,intLinkMonth,intLinkYear)) {

				if (blnIsGreyDate){
					templink='<td align="center" class="cal-GreyDate">' + intLinkDay + '<\/td>';
				}
				else {
					if (isDayToday(intLinkDay)) {
						if (!(bViewOnly)) {
							templink='<td align="center" class="cal-DayCell">' + '<a class="cal-TodayLink" onmouseover="self.status=\' \';return true" href="javascript:'+this.varName+'.changeDay(' + intLinkDay + ');">' + intLinkDay + '<\/a>' +'<\/td>';
						}
						else {
							templink='<td align="center" class="cal-DayCell"><span class="cal-Today">' + intLinkDay + '<\/span><\/td>';
						}
					}
					else {
						if (!(bViewOnly)) {
							templink='<td align="center" class="cal-DayCell">' + '<a class="cal-DayLink" onmouseover="self.status=\' \';return true" href="javascript:'+this.varName+'.changeDay(' + intLinkDay + ');">' + intLinkDay + '<\/a>' +'<\/td>';
						}
						else {
							templink='<td align="center" class="cal-DayCell"><span class="cal-Day">' +  intLinkDay  +'<\/span><\/td>';
						}
					}
				}
			}
			else {
				templink='<td align="center" class="cal-GreyInvalidDate">'+ intLinkDay + '<\/td>';
			}
		}
		return templink;
	}
	this.getDayLink=getDayLink;


	// EXTRA Private FUNCTIONS ===============================================================

	function toggleCombos(showHow){
		var i; var j;
		var cboX; var cboY;
		for (i=0;i<document.forms.length;i++) {
			for (j=0;j<document.forms[i].elements.length;j++) {
				if (document.forms[i].elements[j].tagName == "SELECT") {
					if (document.forms[i].name != "spiffyCal") {
						cboX = getOffsetLeft(document.forms[i].elements[j]);
						cboY = getOffsetTop(document.forms[i].elements[j]);
							if ( ((cboX>=scX-15) && (cboX<=scX+200)) && ((cboY>=scY-15) && (cboY<=scY+145)) )                 
								document.forms[i].elements[j].style.visibility=showHow;
							//Check for right hand side overlapping.
							cboX = cboX + parseInt(document.forms[i].elements[j].style.width);
							cboY=cboY+15;//cbo height (default)
							if ( ((cboX>=scX+15) && (cboX<=scX+200)) && ((cboY>=scY-15) && (cboY<=scY+145)) )                 
								document.forms[i].elements[j].style.visibility=showHow;
					}
				}
			}
		}
	}



	function isDayToday(intWhatDay) {
		if ((dteCur.getFullYear() == dteToday.getFullYear()) && (dteCur.getMonth() == dteToday.getMonth()) && (intWhatDay == dteToday.getDate())) {
			return true;
		}
		else {
			return false;
		}
	}


	function isDayValid(intWhatDay, intWhatMonth, intWhatYear){
		dteCur.setDate(intWhatDay);
		dteCur.setMonth(intWhatMonth);
		dteCur.setFullYear(intWhatYear);
		dteCur.setHours(0);dteCur.setMinutes(0);dteCur.setSeconds(0);dteCur.setMilliseconds(0);
		if ((dteCur>=this.minDate) && (dteCur<=this.maxDate)) {
			return true;
		}
		else {
			return false;
		}
	}
	this.isDayValid=isDayValid;
	
	calMgr.addCalendar(this);
	
	blnInConstructor=false;
}



// Utility functions----------------------------------


function quote(sWhat) {
	return '\''+sWhat+'\'';
}


function getOffsetLeft (el) {
	var ol = el.offsetLeft;
	while ((el = el.offsetParent) != null)
		ol += el.offsetLeft;
	return ol;
}


function getOffsetTop (el) {
	var ot = el.offsetTop;
	while((el = el.offsetParent) != null)
		ot += el.offsetTop;
	return ot;
}

function calClick() {
	window.focus();
}

function domlay(id,trigger,lax,lay,content) {
	/*
	 * Cross browser Layer visibility / Placement Routine
	 * Done by Chris Heilmann (mail@ichwill.net) 
	 * http://www.ichwill.net/mom/domlay/
	 * Feel free to use with these lines included!
	 * Created with help from Scott Andrews.
	 * The marked part of the content change routine is taken
	 * from a script by Reyn posted in the DHTML
	 * Forum at Website Attraction and changed to work with
	 * any layername. Cheers to that!
	 * Welcome DOM-1, about time you got included... :)
	 */
	// Layer visible
	if (trigger=="1"){
		if (document.layers) document.layers[''+id+''].visibility = "show"
		else if (document.all) document.all[''+id+''].style.visibility = "visible"
		else if (document.getElementById) document.getElementById(''+id+'').style.visibility = "visible"                
		}
	// Layer hidden
	else if (trigger=="0"){
		if (document.layers) document.layers[''+id+''].visibility = "hide"
		else if (document.all) document.all[''+id+''].style.visibility = "hidden"
		else if (document.getElementById) document.getElementById(''+id+'').style.visibility = "hidden"             
		}
	// Set horizontal position  
	if (lax){
		if (document.layers){document.layers[''+id+''].left = lax}
		else if (document.all){document.all[''+id+''].style.left=lax}
		else if (document.getElementById){document.getElementById(''+id+'').style.left=lax+"px"}
		}
	// Set vertical position
	if (lay){
		if (document.layers){document.layers[''+id+''].top = lay}
		else if (document.all){document.all[''+id+''].style.top=lay}
		else if (document.getElementById){document.getElementById(''+id+'').style.top=lay+"px"}
		}
	// change content

	if (content){
	if (document.layers){
		sprite=document.layers[''+id+''].document;
		// add father layers if needed! document.layers[''+father+'']...
		sprite.open();
		sprite.write(content);
		sprite.close();
		}
	else if (document.all) document.all[''+id+''].innerHTML = content;  
	else if (document.getElementById){
		//Thanx Reyn!
		rng = document.createRange();
		el = document.getElementById(''+id+'');
		rng.setStartBefore(el);
		htmlFrag = rng.createContextualFragment(content)
		while(el.hasChildNodes()) el.removeChild(el.lastChild);
		el.appendChild(htmlFrag);
		// end of Reyn ;)
		}
	}
}


function makeArray0() {
	for (i = 0; i<makeArray0.arguments.length; i++)
		this[i] = makeArray0.arguments[i];
}

//---------------------------------------

