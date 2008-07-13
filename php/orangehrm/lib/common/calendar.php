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

$sysConf = new sysConf();

include 'yui.php';
?>
<script type="text/javascript" src="<?php echo $_SESSION['WPATH']; ?>/scripts/time.js"></script>

<script type="text/javascript" src="<?php echo $_SESSION['WPATH']; ?>/scripts/calendar/calendar.js"></script>
<script type="text/javascript">
YAHOO.namespace("OrangeHRM.time");

YAHOO.OrangeHRM.calendar.format = '<?php echo LocaleUtil::convertToXpDateFormat($sysConf->getDateFormat())?>';
YAHOO.OrangeHRM.calendar.formatHint.format = '<?php echo $sysConf->getDateInputHint(); ?>';

YAHOO.OrangeHRM.time.format = '<?php echo LocaleUtil::convertToXpDateFormat($sysConf->getTimeFormat())?>';
</script>
