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
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/viewAnniversaries'));
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/viewAnniversaries'));
?>
<div class ="rightBarBodyAll">
    <div class="rightBarHeading" id="rightBarHeadingAnniv"><?php echo __('UPCOMING ANNIVERSARIES'); ?> 
        <img id="moreAniversary" src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/more2.png"); ?>" 
             height="30px" width="30px"/>
        <img id="lessAniversary" src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/less2.png"); ?>" 
             height="30px" width="30px"/>
    </div>
    <div class ="rightBarBody">
        <ul class="toggling" style="display:none;" id="upcomingAnnivList">    
            <?php if (count($anniversaryEmpList) == 0) { ?>
                <li id="anniversaryPostNull">
                    <div id="anniversaryUserName">
                        <label href="#" class="name" id="name2">
                            <?php echo __("No Upcoming Anniversaries For Next 30 Days"); ?>
                        </label>
                    </div>
                </li>
            <?php } ?>
            <?php
            foreach ($anniversaryEmpList as $employee) {
                if ($employee->getTerminationId() == NULL) {
                    ?>
                    <li id="anniversaryPost">
                        <div id="annivPicAndNameContainer">
                            <div id="annivProfilePicContainer">
                                <img alt="<?php echo __("Employee Photo"); ?>" 
                                     src="<?php echo url_for("buzz/viewPhoto?empNumber=" . $employee->getEmpNumber()); ?>" id="empPic"/>
                            </div>  
                            <?php $employeeFirstAndLastNames = $employee->getFirstName() . " " . $employee->getLastName(); ?>
                            <div id="anniversaryUserName" title="<?php echo $employeeFirstAndLastNames; ?>">
                                    <?php
                                    if (strlen($employeeFirstAndLastNames) > 18) {
                                        echo substr($employeeFirstAndLastNames, 0, 18) . '...';
                                    } else {
                                        echo $employeeFirstAndLastNames;
                                    }
                                    ?>
                            </div>        
                        </div>
                        <br>
                        <br>
                        <?php $jobTitle = $employee->getJobTitleName(); ?>
                        <div id="birthdayUserJobTitle" title="<?php echo $jobTitle; ?>">
                            <?php
                            if (strlen($jobTitle) > 25) {
                                echo substr($jobTitle, 0, 25) . '...';
                            }else{
                                echo $jobTitle;
                            }
                            ?>
                        </div>
                        <div id="annivDate">
                            <?php echo __(date('F', strtotime($employee->getJoinedDate()))).' '. date('d', strtotime($employee->getJoinedDate())); ?>
                        </div>
                        <div id="anniversaryText"><?php
                            $years = (date('Y') - (date('Y', strtotime($employee->getJoinedDate()))));
                            ?><div id="yearsBox" ><?php
                            if ($years > 1) {
                                echo $years.' '.__('years');
                            } else {
                                echo $years.' '.__('year');
                            }
                            ?>
                            </div>
                            <div id="joinedDate">
                                <?php echo __("Joined Date") . " : " . set_datepicker_date_format($employee->getJoinedDate()); ?>
                            </div>
                        </div>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
    </div>
</div>