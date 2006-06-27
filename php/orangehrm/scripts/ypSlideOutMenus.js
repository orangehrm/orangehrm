/*****************************************************
 * ypSlideOutMenu
 * 3/04/2001
 * 
 * a nice little script to create exclusive, slide-out
 * menus for ns4, ns6, mozilla, opera, ie4, ie5 on 
 * mac and win32. I've got no linux or unix to test on but 
 * it should(?) work... 
 *
 * --youngpup--
 *****************************************************/

ypSlideOutMenu.Registry = []
ypSlideOutMenu.aniLen = 650
ypSlideOutMenu.hideDelay = 325
ypSlideOutMenu.minCPUResolution = 10

// constructor
function ypSlideOutMenu(id, dir, left, top, width, height)
{
	this.ie  = document.all ? 1 : 0
	this.ns4 = document.layers ? 1 : 0
	this.dom = document.getElementById ? 1 : 0

	if (this.ie || this.ns4 || this.dom) {
		this.id			 = id
		this.dir		 = dir
		this.orientation = dir == "left" || dir == "right" ? "h" : "v"
		this.dirType	 = dir == "right" || dir == "down" ? "-" : "+"
		this.dim		 = this.orientation == "h" ? width : height
		this.hideTimer	 = false
		this.aniTimer	 = false
		this.open		 = false
		this.over		 = false
		this.startTime	 = 0

		// global reference to this object
		this.gRef = "ypSlideOutMenu_"+id
		eval(this.gRef+"=this")

		// add this menu object to an internal list of all menus
		ypSlideOutMenu.Registry[id] = this

		var d = document
		d.write('<style type="text/css">')
		d.write('#' + this.id + 'Container { visibility:hidden; ')
		d.write('left:' + left + 'px; ')
		d.write('top:' + top + 'px; ')
		d.write('overflow:hidden; }')
		d.write('#' + this.id + 'Container, #' + this.id + 'Content { position:absolute; ')
		d.write('width:' + width + 'px; ')
		d.write('height:' + height + 'px; ')
		d.write('clip:rect(0 ' + width + ' ' + height + ' 0); ')
		d.write('}')
		d.write('</style>')

		this.load()
	}
}

ypSlideOutMenu.prototype.load = function() {
	var d = document
	var lyrId1 = this.id + "Container"
	var lyrId2 = this.id + "Content"
	var obj1 = this.dom ? d.getElementById(lyrId1) : this.ie ? d.all[lyrId1] : d.layers[lyrId1]
	if (obj1) var obj2 = this.ns4 ? obj1.layers[lyrId2] : this.ie ? d.all[lyrId2] : d.getElementById(lyrId2)
	var temp

	if (!obj1 || !obj2) window.setTimeout(this.gRef + ".load()", 100)
	else {
		this.container	= obj1
		this.menu		= obj2
		this.style		= this.ns4 ? this.menu : this.menu.style
		this.homePos	= eval("0" + this.dirType + this.dim)
		this.outPos		= 0
		this.accelConst	= (this.outPos - this.homePos) / ypSlideOutMenu.aniLen / ypSlideOutMenu.aniLen 

		// set event handlers.
		if (this.ns4) this.menu.captureEvents(Event.MOUSEOVER | Event.MOUSEOUT);
//		this.menu.onmouseover = new Function("ypSlideOutMenu.showMenu('" + this.id + "')")
//		this.menu.onmouseout = new Function("ypSlideOutMenu.hideMenu('" + this.id + "')")

		//set initial state
		this.endSlide()
	}
}
	
ypSlideOutMenu.showMenu = function(id)
{
	var reg = ypSlideOutMenu.Registry
	var obj = ypSlideOutMenu.Registry[id]
	
	if (obj.container) {
		obj.over = true

		// if this menu is scheduled to close, cancel it.
		if (obj.hideTimer) { reg[id].hideTimer = window.clearTimeout(reg[id].hideTimer) }

		// if this menu is closed, open it.
		if (!obj.open && !obj.aniTimer) reg[id].startSlide(true)
	}
}

ypSlideOutMenu.hideMenu = function(id)
{
	// schedules the menu to close after <hideDelay> ms, which
	// gives the user time to cancel the action if they accidentally moused out
	var obj = ypSlideOutMenu.Registry[id]
	   if (obj.container) {
		   if (obj.hideTimer) window.clearTimeout(obj.hideTimer)
		   obj.hideTimer = window.setTimeout("ypSlideOutMenu.hide('" + id + "')", ypSlideOutMenu.hideDelay);
	   }
}

ypSlideOutMenu.hide = function(id)
{
	var obj = ypSlideOutMenu.Registry[id]
	obj.over = false

	if (obj.hideTimer) window.clearTimeout(obj.hideTimer)
	
	// flag that this scheduled event has occured.
	obj.hideTimer = 0

	// if this menu is open, close it.
	if (obj.open && !obj.aniTimer) obj.startSlide(false)
}

ypSlideOutMenu.prototype.startSlide = function(open) {
	this[open ? "onactivate" : "ondeactivate"]()
	this.open = open
	if (open) this.setVisibility(true)
	this.startTime = (new Date()).getTime()	
	this.aniTimer = window.setInterval(this.gRef + ".slide()", ypSlideOutMenu.minCPUResolution)
}

ypSlideOutMenu.prototype.slide = function() {
	var elapsed = (new Date()).getTime() - this.startTime
	if (elapsed > ypSlideOutMenu.aniLen) this.endSlide()
	else {
		var d = Math.round(Math.pow(ypSlideOutMenu.aniLen-elapsed, 2) * this.accelConst)
		if (this.open && this.dirType == "-")		d = -d
		else if (this.open && this.dirType == "+")	d = -d
		else if (!this.open && this.dirType == "-")	d = -this.dim + d
		else										d = this.dim + d

		this.moveTo(d)
	}
}

ypSlideOutMenu.prototype.endSlide = function() {
	this.aniTimer = window.clearTimeout(this.aniTimer)
	this.moveTo(this.open ? this.outPos : this.homePos)
	if (!this.open) this.setVisibility(false)
	if ((this.open && !this.over) || (!this.open && this.over)) {
		this.startSlide(this.over)
	}
}

ypSlideOutMenu.prototype.setVisibility = function(bShow) { 
	var s = this.ns4 ? this.container : this.container.style
	s.visibility = bShow ? "visible" : "hidden"
}
ypSlideOutMenu.prototype.moveTo = function(p) { 
	this.style[this.orientation == "h" ? "left" : "top"] = this.ns4 ? p : (p) + "px"
}
ypSlideOutMenu.prototype.getPos = function(c) {
	return parseInt(this.style[c])
}

// events
ypSlideOutMenu.prototype.onactivate		= function() { }
ypSlideOutMenu.prototype.ondeactivate	= function() { }