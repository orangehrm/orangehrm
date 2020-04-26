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
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/viewBuzzSuccess'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/loggedInUserDetails'));
?>

<div id="buzzDetailContainer">
    <form id="buzzSearchForm">
        <a href="<?php echo url_for("buzz/viewBuzz"); ?>" id="buzz-logo">
            <img height="60px" src="<?php echo plugin_web_path('orangehrmBuzzPlugin', 'images/buzz_logo_small.png'); ?>">
        </a>
        <div id="buzz-search-div">
            <?php
            $searchForm = new BuzzEmployeeSearchForm();
            echo $searchForm['emp_name'];
            ?>
            <img id="gotoProfile" height="30px" src="<?php echo plugin_web_path('orangehrmBuzzPlugin', 'images/search.png'); ?>">
        </div>
    </form>

    <div id="profileDetails">
        <div id="leftSide">
            <div id="userName">
                <a class="name headerEmpName" href="<?php echo url_for("buzz/viewProfile?empNumber=" . $empNumber); ?>"><?php echo $name; ?></a>
            </div>
            <div id="companyPosition" >
                <?php echo $jobtitle; ?>
            </div>
            <div id="links">
                <a class="homeLink name headerLink" href= '<?php echo url_for("buzz/viewBuzz"); ?>' >
                    <?php echo __("HOME"); ?>
                </a>
                <a class="ProfileLink name headerLink" href= '<?php echo url_for("buzz/viewProfile?empNumber=" . $empNumber); ?>' >
                    <?php echo __("PROFILE"); ?>
                </a>
                <a class="logoutLink name headerLink" href="<?php echo url_for('buzz/logOut'); ?>">
                    <?php echo __("LOGOUT"); ?>
                </a>
            </div>
        </div>
        <div id="rightSide">
            <a href="<?php echo url_for("buzz/viewProfile?empNumber=" . $empNumber); ?>">
                <img alt="<?php echo __("Employee Photo"); ?>"
                     src="<?php echo url_for("buzz/viewPhoto?empNumber=" . $empNumber); ?>"
                     border="0" id="empPic"/>
            </a>
        </div>
    </div>
</div>

<script type="text/javascript">
    var profilePage = '<?php echo url_for('buzz/viewProfile?empNumber='); ?>';
</script>
