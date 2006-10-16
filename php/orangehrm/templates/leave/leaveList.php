<?php
/*
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
 * all the essential functionalities required for any enterprise. 
 * Copyright (C) 2006 hSenid Software, http://www.hsenid.com
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

/*
 *	Including the language pack
 *
 **/
 
 $lan = new Language();
 
 require_once($lan->getLangPath("leave/leaveList.php")); 
?>
<h3><?php echo $lang_Title?></h3>
<table border="1" cellpadding="2" cellspacing="0">
  <thead>
  	<tr>
    	<th><?php echo $lang_Date;?></th>
    	<th><?php echo $lang_LeaveType;?></th>
    	<th><?php echo $lang_Status;?></th>
    	<th><?php echo $lang_Length;?></th>
    	<th><?php echo $lang_Comments;?></th>
	</tr>
  </thead>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>