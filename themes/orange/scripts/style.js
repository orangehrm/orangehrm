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
 */

/* Style related JavaScript for orange theme */

/**
 * Draw a round border around div's with the given css class 
 * @param className Class Name of div's to be given a round border
 */
function roundBorder(className) {
	var innerClass = 'maincontent';
        var divContent = '';
    var elements = document.getElementsByTagName('div');
    for (i=0;i<getElementLength(elements.length);i++) {
        div = elements[i];
        if (div.className == className) {

			divContent = div.innerHTML;
			div.innerHTML = "";
			
            topdiv = document.createElement('div');
            topdiv.className = "top";
			div.appendChild(topdiv);
            topleft = document.createElement('div');
            topleft.className = "left";
            topdiv.appendChild(topleft);           
            topright = document.createElement('div');
            topright.className = "right";
            topdiv.appendChild(topright);            
            topmiddle = document.createElement('div');
            topmiddle.className = "middle";
            topdiv.appendChild(topmiddle);            			
			
			innerDiv = document.createElement('div');
			innerDiv.className = innerClass;
			innerDiv.innerHTML = divContent;			
			div.appendChild(innerDiv);
						
            bottomdiv = document.createElement('div');
            bottomdiv.className = "bottom";
            div.appendChild(bottomdiv);            
            bottomleft = document.createElement('div');
            bottomleft.className = "left";
            bottomdiv.appendChild(bottomleft);
            bottomright = document.createElement('div');
            bottomright.className = "right";
            bottomdiv.appendChild(bottomright);
            bottommiddle = document.createElement('div');
            bottommiddle.className = "middle";
            bottomdiv.appendChild(bottommiddle);                                    
        }
    }
}

/*
 * Function run when mouse moves over a button
 * Sets className of button to "className classNamehov" 
 */
function moverButton(button) {
	var btnClass = button.className;
	button.className =  btnClass + " " + btnClass + "hov"; 
}

/*
 * Function run when moves moves out of a button
 * Removes the 'hov' className added in moverButton function
 */
function moutButton(button) {
    var classes = button.className.split(" ");
    if (classes.length > 1) {
        button.className = classes[0];
    }
}

/*
 * Function will run when adding rounded borders to the template div tags
 * Reduce the nuber of div counts from 1 when browser version is IE
 */
function getElementLength(length){
    if(ieVersion()==9){
        return length-1;
    }
    return length;   
}

/*
 * Function will run when adding rounded borders to the template div tags
 * Function will return the current version of Internet Explorer in client side 
 */
function ieVersion(){
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf ("MSIE ");
    if (msie > 0){
        return parseInt(ua.substring(msie+5, ua.indexOf(".", msie)));
    }
    return 0;         
}