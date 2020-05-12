<?php
//
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
<?php if ($error == 'no') { ?>

    <?php if ($actions == 'post') { ?>
        <div class="" id='<?php echo 'postlikehidebody_' . $id ?>'>
            <div class="empListAllBlock">
                <div class="empListBlock" >
                    <?php foreach ($employeeList as $employeeDetails) { ?>
                        <div id="employeeView">
                            <?php if ($employeeDetails[BaseBuzzAction::EMP_NUMBER] != "") { ?>
                                <div id="empFirstRaw">
                                    <div id="empProfilePicContainer">
                                        <img alt="<?php echo __("Employee Photo"); ?>" src="<?php echo url_for("buzz/viewPhoto?empNumber=" . $employeeDetails[BaseBuzzAction::EMP_NUMBER]); ?>" border="0" id="empPic"/>
                                    </div>
                                    <div>
                                        <div id="employeeUserName" title="<?php echo $employeeDetails[BaseBuzzAction::EMP_NAME]; ?>">
                                            <div id="empname" title="<?php echo $employeeDetails[BaseBuzzAction::EMP_NAME]; ?>">
                                                <?php
                                                if (strlen($employeeDetails[BaseBuzzAction::EMP_NAME]) > 29) {
                                                    echo substr($employeeDetails[BaseBuzzAction::EMP_NAME], 0, 29) . '...';
                                                } else {
                                                    echo $employeeDetails[BaseBuzzAction::EMP_NAME];
                                                }
                                                ?>              

                                            </div>
                                        </div>
                                        <div id="employeeJobTitle">
                                            <?php echo $employeeDetails[BaseBuzzAction::EMP_JOB_TITLE]; ?>
                                        </div>                                
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div id="empFirstRaw">
                                    <div id="empProfilePicContainer">
                                        <img alt="<?php echo __("Employee Photo"); ?>" src="<?php echo url_for("buzz/viewPhoto?empNumber="); ?>" border="0" id="empPic" height="60" width="60"/>
                                    </div>
                                    <div id="employeeUserName">
                                        <div id="empname"  >
                                            <?php echo __('Admin'); ?>                 

                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </div>
    <?php } else { ?>

        <div class="" id='<?php echo 'postlikehidebody_' . $id ?>'>
            <div class="empListAllBlock">
                <div class="empListBlock" >
                    <?php foreach ($employeeList as $employeeDetails) { ?>
                        <div id="employeeView">
                            <?php if ($employeeDetails[BaseBuzzAction::EMP_NUMBER] != "") { ?>
                                <div id="empFirstRaw">
                                    <div id="empProfilePicContainer">
                                        <img alt="<?php echo __("Employee Photo"); ?>" src="<?php echo url_for("buzz/viewPhoto?empNumber=" . $employeeDetails[BaseBuzzAction::EMP_NUMBER]); ?>" border="0" id="empPic" height="60" width="60"/>
                                    </div>
                                    <div id="employeeUserName">
                                        <div id="empname"  >
                                            <?php
                                            if (strlen($employeeDetails[BaseBuzzAction::EMP_NAME]) > 29) {
                                                echo substr($employeeDetails[BaseBuzzAction::EMP_NAME], 0, 29) . '...';
                                            } else {
                                                echo $employeeDetails[BaseBuzzAction::EMP_NAME];
                                            }
                                            ?>                

                                        </div>
                                        <div id="employeeJobTitle">
                                            <?php echo $employeeDetails[BaseBuzzAction::EMP_JOB_TITLE]; ?>
                                        </div>    
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div id="empFirstRaw">
                                    <div id="empProfilePicContainer">
                                        <img alt="<?php echo __("Employee Photo"); ?>" src="<?php echo url_for("buzz/viewPhoto?empNumber="); ?>" border="0" id="empPic" height="60" width="60"/>
                                    </div>
                                    <div id="employeeUserName">
                                        <div id="empname"  >
                                            <?php echo __('Admin'); ?>                 

                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </div>
    <?php } ?>
<?php } else { ?>
    <?php if ($actions == 'post') { ?>
        <div class="" id='<?php echo 'postlikehidebody_' . $id ?>'>
            <div id ='errorFirstRow'>
                <?php echo __("This share has been deleted or you do not have permission to perform this action"); ?>
            </div> 
            <div id ='errorFirstRow'>
                <input type="button" class="btnBackHide" name="btnSaveDependent" id='<?php echo 'btnhideLike_' . $id ?>' value="<?php echo __attr("Back"); ?> " />
            </div>

        </div>
    <?php } else { ?>
        <div class="" id='<?php echo 'postlikehidebody_' . $id ?>'>
            <div id ='errorFirstRow'>
                <?php echo __("This comment has been deleted or you do not have permission to perform this action"); ?>
            </div>
            <div id="errorFirstRow">
                <input type="button" class="btnBackHideComment" name="btnSaveDependent" id='<?php echo 'btnhideLikecomment_' . $id ?>' value="<?php echo __attr("Back"); ?> " />
            </div>

        </div>
    <?php } ?>
<?php } ?>