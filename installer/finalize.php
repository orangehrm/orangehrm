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
?>
<script language="JavaScript">
    function login() {
        document.frmInstall.actionResponse.value = 'LOGIN';
        document.frmInstall.submit();
    }
</script>
<link href="installer/style.css" rel="stylesheet" type="text/css" />

<style>
    ul.registration li {
        color:#dc8701;
        height: 11px;
    }
    ul.registration li span {
        color:black;
    }

    .registration {

    }
    .wrapper {
        display: block;
    }

    .wrapper_content_div {
        float: left;
        margin: 5px 30px 0px 0px;
    }
    .clear {
        clear:both;
    }

</style>

<div style="display: block;" class="wrapper">
    <h2>Step 7: Final</h2>
    <p>You have successfully installed OrangeHRM</p>
    <div class="wrapper" style="width: 900px;">
        <div>
            <input name="button" type="button" onclick="login();" value="Finish" tabindex="10"/>
        </div>
    </div>
    <br class="clear"/>