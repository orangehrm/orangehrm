// DynLayer Glide Methods
// alternative DynLayer animation methods with an acceleration effect
// 19990531

// Copyright (C) 1999 Dan Steinman
// Distributed under the terms of the GNU Library General Public License
// Available at http://www.dansteinman.com/dynapi/

function DynLayerGlideTo(startSpeed,endSpeed,endx,endy,angleinc,speed,fn) {
	if (endx==null) endx = this.x
	if (endy==null) endy = this.y
	var distx = endx-this.x
	var disty = endy-this.y
	this.glideStart(startSpeed,endSpeed,endx,endy,distx,disty,angleinc,speed,fn)
}
function DynLayerGlideBy(startSpeed,endSpeed,distx,disty,angleinc,speed,fn) {
	var endx = this.x + distx
	var endy = this.y + disty
	this.glideStart(startSpeed,endSpeed,endx,endy,distx,disty,angleinc,speed,fn)
}
function DynLayerGlideStart(startSpeed,endSpeed,endx,endy,distx,disty,angleinc,speed,fn) {
	if (this.glideActive) return
	if (endx==this.x) var slantangle = 90
	else if (endy==this.y) var slantangle = 0
	else var slantangle = Math.abs(Math.atan(disty/distx)*180/Math.PI)
	if (endx>=this.x) {
		if (endy>this.y) slantangle = 360-slantangle
	}
	else {
		if (endy>this.y) slantangle = 180+slantangle
		else slantangle = 180-slantangle
	}
	slantangle *= Math.PI/180
	var amplitude = Math.sqrt(Math.pow(distx,2) + Math.pow(disty,2))
	if (!fn) fn = null
	this.glideActive = true
	if (startSpeed == "fast") {
		if (endSpeed=="fast") this.glide(1,amplitude/2,0,90,this.x,this.y,slantangle,endx,endy,distx,disty,angleinc,speed,fn)
		else this.glide(0,amplitude,0,90,this.x,this.y,slantangle,endx,endy,distx,disty,angleinc,speed,fn)
	}
	else {
		if (endSpeed=="fast") this.glide(0,amplitude,-90,0,this.x+distx,this.y+disty,slantangle,endx,endy,distx,disty,angleinc,speed,fn)
		else this.glide(0,amplitude/2,-90,90,this.x+distx/2,this.y+disty/2,slantangle,endx,endy,distx,disty,angleinc,speed,fn)
	}
}
function DynLayerGlide(type,amplitude,angle,endangle,centerX,centerY,slantangle,endx,endy,distx,disty,angleinc,speed,fn) {
	if (angle < endangle && this.glideActive) {
		angle += angleinc
		var u = amplitude*Math.sin(angle*Math.PI/180)
		var x = centerX + u*Math.cos(slantangle)
		var y = centerY - u*Math.sin(slantangle)
		this.moveTo(x,y)
		this.onGlide()
		if (this.glideActive) setTimeout(this.obj+'.glide('+type+','+amplitude+','+angle+','+endangle+','+centerX+','+centerY+','+slantangle+','+endx+','+endy+','+distx+','+disty+','+angleinc+','+speed+',\''+fn+'\')',speed)
		else this.onGlideEnd()
	}
	else {
		if (type==1) this.glide(0,amplitude,-90,0,this.x+distx/2,this.y+disty/2,slantangle,endx,endy,distx,disty,angleinc,speed,fn)
		else {
			this.glideActive = false
			this.moveTo(endx,endy)
			this.onGlide()
			this.onGlideEnd()
			eval(fn)
		}
	}
}
DynLayerGlideInit = new Function()
DynLayer.prototype.glideInit = new Function()
DynLayer.prototype.glideTo = DynLayerGlideTo
DynLayer.prototype.glideBy = DynLayerGlideBy
DynLayer.prototype.glideStart = DynLayerGlideStart
DynLayer.prototype.glide = DynLayerGlide
DynLayer.prototype.onGlide = new Function()
DynLayer.prototype.onGlideEnd = new Function()
