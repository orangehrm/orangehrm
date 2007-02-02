<script>
<!--
function addEvent(obj, evType, fn) {
    if (obj.addEventListener) {
        obj.addEventListener(evType, fn, true);
        return true;
    } else if (obj.attachEvent) {
        var r = obj.attachEvent("on"+evType, fn);
        return r;
    } else {
        return false;
    }
}

/*
 * Javascript function to dynamically add css classed needed for a rounded
 * box. Based on http://www.dragon-labs.com/articles/octopus/
 */
function initOctopus() {
    classTree  = ["north","east","south","west","ne","se","sw","nw", "roundbox_content"];
    className="roundbox";

    tempdivs = [];
    divs = document.getElementsByTagName('div');
    for (i=0;i<divs.length;i++) {
        cdiv = divs[i];
        if (cdiv.className == className) {
            tempinner = cdiv.innerHTML;
            cdiv.innerHTML = "";
            prevdiv = cdiv;
            for (a=0; a < 9; a++) {
                tempdivs[a] = document.createElement('div');
                tempdivs[a].className = classTree[a];
                prevdiv.appendChild(tempdivs[a]);
                prevdiv = tempdivs[a];
            }
            prevdiv.innerHTML = tempinner;
        }
    }
}

-->
</script>

