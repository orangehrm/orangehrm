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
 */
?>
<style type="text/css">

    #buzzHeader{
        font-size: 30px; 
        position: fixed; 
        width: 100%;
        padding-top: 8.5px;
        padding-bottom: 7px;
        z-index: 9999; 
        color: white;
        height: 68px;
    }

    #buzzDetailContainer {
        width: 980px;
        height: 68px;
        margin: 0 auto;
        overflow: hidden;
    }

</style>
<?php include_partial('global/header'); ?>

</head>
<body>
    <div id="buzzHeader" class="ac_over">
        <?php include_component('buzz', 'loggedInUserDetails', array()); ?>
    </div>

    <div id="wrapper">

        <div id="content">

            <?php echo $sf_content ?>
        </div> <!-- content -->

    </div> <!-- wrapper -->
</body>
