<?php
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
 */

$heading = $records[0];
$years = $records[1];
$action = $records[2]
?>
<script type="text/javascript">
//<![CDATA[
function viewYear() {
	window.location = $('frmSelectYear').action+$('cmbYear').value;
}
//]]>
</script>
<div class="formpage">
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo ${"lang_Benefits_$heading"}; ?></h2></div>

        <form action="?benefitcode=Benefits&amp;action=<?php echo $action; ?>&amp;year=" method="post" id="frmSelectYear" onsubmit="viewYear(); return false;">        
			<label for="cmbYear"><?php echo $lang_Benefits_ChooseYear; ?></label>
            <select name="cmbYear" id="cmbYear" class="formSelect">
			<?php for ($i=0; $i<count($years); $i++) { ?>
			    <option value="<?php echo $years[$i]; ?>"><?php echo $years[$i]; ?></option>
			<?php } ?>
			</select>
            <br class="clear"/>
                
            <div class="formbuttons">
                <input type="submit" class="viewbutton" id="btnView" 
                    onmouseover="moverButton(this);" onmouseout="moutButton(this);"                          
                    value="<?php echo $lang_Common_View;?>" />                        
            </div>
            <br class="clear"/>
        </form>
    </div>
    <script type="text/javascript">
    //<![CDATA[
        if (document.getElementById && document.createElement) {
            roundBorder('outerbox');                
        }
    //]]>
    </script>
</div> 