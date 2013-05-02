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

$(document).ready(function (){
    
    $('#expandJobList').click(function() {
        if($('.vacancyDescription:visible').length !== $('.vacancyDescription').length) {
            $('.vacancyDescription').hide();            
            $('.vacancyShortDescription').hide();
            $('.vacancyDescription').toggle('fast');
            $('.plusMark').hide();
            $('.minusMark').show();
        }
    });
    
    $('#collapsJobList').click(function() {
        if($('.vacancyDescription:visible').length !== 0) {
            $('.vacancyDescription').show();
            $('.vacancyShortDescription').show();
            $('.vacancyDescription').toggle('fast');
            $('.plusMark').show();
            $('.minusMark').hide();
        }
    });
    
    $('.vacancyTitle').click(function() {
        if($(this).next().is(':visible')) {
            $(this).next().hide();
        } else {
            $(this).next().show();
        }
        $(this).next().next().toggle('fast');
        $(this).parent().prev().children('.minusMark').toggle();
        $(this).parent().prev().children('.plusMark').toggle();
    });
    
    $('.minusMark, .plusMark').click(function() {        
        if($(this).parent().next().children('.vacancyShortDescription').is(':visible')) {
            $(this).parent().next().children('.vacancyShortDescription').hide();
        } else {
            $(this).parent().next().children('.vacancyShortDescription').show();
        }
        $(this).parent().next().children('.vacancyDescription').toggle();        
        if($(this).attr('class') == 'plusMark') {
            $(this).parent().children('.plusMark').hide();
            $(this).parent().children('.minusMark').show();
        } else {
            $(this).parent().children('.plusMark').show();
            $(this).parent().children('.minusMark').hide();
        }
//        $(this).parent().next().children('.vacancyTitle').css('background-color', '#d5d5d5');
    });
    
    $('.minusMark, .plusMark').hover(function() {
//        $(this).parent().next().children('.vacancyTitle').css('background-color', '#d5d5d5');
    },function() {
//        $(this).parent().next().children('.vacancyTitle').css('background-color', '#fff');
    });
    
    $('.apply').click(function() {
	//alert($(this).next().attr("href"));
        window.location.href = $(this).next().attr("href");
    });
    
    $('.verticalLine:last').hide();
    
});

/**
 * Draw a round border around div's with the given css class 
 * @param className Class Name of div's to be given a round border
 */
function roundBorder(className) {
    
    var innerClass = 'maincontent';
    var elements = document.getElementsByTagName('div');
    for (i=0;i<elements.length;i++) {
        
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

