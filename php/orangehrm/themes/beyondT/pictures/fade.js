

document.onmouseover = domouseover;
document.onmouseout = domouseout;


function domouseover() {
  if(document.all){
  srcElement = window.event.srcElement;
  if (srcElement.className.indexOf("fade") > -1) {
        var linkName = srcElement.name;
      fadein(linkName);
      }
      }
}

function domouseout() {
  if (document.all){
  srcElement = window.event.srcElement;
  if (srcElement.className.indexOf("fade") > -1) {
        var linkName = srcElement.name;
      fadeout(linkName);
      }
      }
}

function makearray(n) {
    this.length = n;
    for(var i = 1; i <= n; i++)
        this[i] = 0;
    return this;
}

hexa = new makearray(16);
for(var i = 0; i < 10; i++)
    hexa[i] = i;
hexa[10]="a"; hexa[11]="b"; hexa[12]="c";
hexa[13]="d"; hexa[14]="e"; hexa[15]="f";

function hex(i) {
    if (i < 0)
        return "00";
    else if (i > 255)
        return "ff";
    else
       return "" + hexa[Math.floor(i/16)] + hexa[i%16];}

function setbgColor(r, g, b, element) {
      var hr = hex(r); var hg = hex(g); var hb = hex(b);
      element.style.color = "#"+hr+hg+hb;
}

function fade(sr, sg, sb, er, eg, eb, step, direction, element){
    for(var i = 0; i <= step; i++) {
setTimeout("setbgColor(Math.floor(" +sr+ " *(( " +step+ " - " +i+ " )/ " +step+ " ) + " +er+ " * (" +i+ "/" +step+ ")),Math.floor(" +sg+ " * (( " +step+ " - " +i+ " )/ " +step+ " ) + " +eg+ " * (" +i+ "/" +step+ ")),Math.floor(" +sb+ " * ((" +step+ "-" +i+ ")/" +step+ ") + " +eb+ " * (" +i+ "/" +step+ ")),"+element+");",i*step);
    }
}



/*-----------------=[fadeout]=----------------------
||Fades the text from one color to another color   ||
||when the mouse moves off of the link.            ||
---------------------------------------------------*/

function fadeout(element) {

/*--------------------------------------------------
||Example:                                         ||
||                                                 ||
||fade(255,150,0, 255,255,255, 180, 1, element);    ||
||                                                 ||
||Explanation:                                     ||
||                                                 ||
||RGB (red, green, blue) values of first color     ||
||(color to start at), then RGB values of second   ||
||color (color to fade to). For my site, I have    ||
||the first color (255,150,0), which is orange,    ||
||and the second color (255,255,255), which is     ||
||white.  Therefore it fades from orange to white  ||
||when the mouse moves off.                        ||
||                                                 ||
||The 30 parameter is the delay time: decrease     ||
||to make it go quicker and increase it to go      ||
||faster.                                          ||
||                                                 ||
||The last two parameters shouldn't be messed with.||
---------------------------------------------------*/
          
    fade(255,255,255, 131,104,22, 20, 1, element);
}

/*------------------=[fadein]=----------------------
||Fades the text from one color to another color   ||
||when the mouse moves over the link.              ||
||-------------------------------------------------*/

function fadein(element) {

/*--------------------------------------------------
||Example:                                         ||
||                                                 ||
||fade(255,255,255, 255,150,0, 180, 1, element);    ||
||                                                 ||
||Explanation:                                     ||
||                                                 ||
||RGB (red, green, blue) values of first color     ||
||(color to start at), then RGB values of second   ||
||color (color to fade to). For my site, I have    ||
||the first color (255,255,255), which is white,   ||
||and the second color (255,150,0), which is or-   ||
||ange.  Therefore it fades from white to orange   ||
||when the mouse moves over the link.              ||
||                                                 ||
||The 23 parameter is the delay time: decrease to  ||
||make it go quicker and increase it to go faster. ||
||In this case, the fading will be slightly quick- ||
||er when the mouse moves over the link than when  ||
||the mouse moves off (which is set to 30, in my   ||
||case).                                           ||
||                                                 ||
||The last two parameters shouldn't be messed with.||
---------------------------------------------------*/

    fade(124,218,254, 26,44,1, 12, 1, element);
}
/*ignore this >>>>*/
function fadeIn2(id){
	fade(26,44,1, 222,209,169, 25, 1, id);
}

function fadeOut2(id){
	fade(222,209,169, 255,255,255, 29, 1, id);
}
/*<<<<< stop ignoring =)*/

/*---------------=[final note]=---------------------
||Now, once you have customized your fading colors,||  
||you need to include your customized .js file on  ||  
||every page that you want to use it in. You can   ||
||include javascript files using this syntax (in   ||
||the head of a document):                         ||      
||<script src="fade.js" language="Javascript">     ||
||</script>                                        ||
||                                                 ||
||Now that you have the file included, you need to ||   
||setup your links a small bit.  Each link that you||  
||want to fade needs to have it's own _unique_ Name||  
||and must use the fade class.                     ||  
||                                                 ||                                                   
||Example:                                         ||
||                                                 || 
||<a href="blah.html" name="fading_link_1" class=  ||
||"fade">click here</a>                            ||
||                                                 ||
||Also, the link must be plain text.  This means   ||
||that you can't have <b>'s, <i>'s, <font>'s, etc. ||
||inside of the link.                              ||
||                                                 ||
||Example of what not to do:                       ||
||                                                 ||
||<a href="blah.html" name="fading_link_1" class=  ||
||"fade"><b>click</b> here</a>                     ||
||                                                 ||
||Have fun!                                        ||
||-Anarchos-                                       ||
---------------------------------------------------*/
