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

include_once('systemConfigurationHelper.php');
$systemConfigurationHelper = new systemConfigurationHelper();

?>

<link href="style.css" rel="stylesheet" type="text/css" />

<div>
    <h2>Step 4: System Configuration</h2>
    <form id="test" class="registrationFormBody" novalidate="false"></form>

    <p>Please fill in your Organization details and Admin Username and Password for the Administrator login. </p>

    <form id="registrationForm" name="registrationForm" novalidate="false" method="post" action="../install.php">
        <h3>Instance Creation</h3>
        <input type="hidden" id="actionResponse" name="actionResponse" value=""/>
        <input type="hidden" id="type" name="type" value="0"/>
        <label>Organization Name <em>*</em></label>
        <input type="text" class="organizationName" id="organizationName" name="organizationName"><br>

        <label>Country <em>*</em></label>
        <select class="country" id="country" name="country">
            <option value=""><?php echo "-Select-"?></option>
            <?php
            $countries = $systemConfigurationHelper->getCountryList();

            foreach ($countries as $key => $country) {
                ?>
                <option value="<?php echo $key?>"><?php echo $country?></option>
                <?php
            }
            ?>
        </select><br>

        <label>Language</label>
        <select class="language" id="language" name="language">
            <option value=""><?php echo "-Select-"?></option>
            <?php
            $languages = $systemConfigurationHelper->getLanguageList();

            foreach ($languages as $key => $language) {
                ?>
                <option value="<?php echo $key?>"><?php echo $language?></option>
                <?php
            }
            ?>
        </select><br>

        <label>Timezone</label>
        <select class="timezone" id="timezone" name="timezone">
            <option value=""><?php echo "-Select-"?></option>
            <?php
            $timezones = $systemConfigurationHelper->getTimeZoneList();

            foreach ($timezones as $key => $timezone) {
                ?>
                <option value="<?php echo $key?>"><?php echo $key?></option>
                <?php
            }
            ?>
        </select><br>

        <br>
        <h3>Admin User Creation</h3>

        <label>Employee Name <em>*</em></label>
        <input type="text" class="adminEmployeeFirstName" id="adminEmployeeFirstName" name="adminEmployeeFirstName" placeholder="First Name"> <input type="text" class="adminEmployeeLastName" id="adminEmployeeLastName" name="adminEmployeeLastName" placeholder="Last Name"><br>

        <label>Email Address <em>*</em></label>
        <input type="text" class="organizationEmailAddress" id="organizationEmailAddress" name="organizationEmailAddress"><br>

        <label>Contact Number</label>
        <input type="text" class="contactNumber" id="contactNumber" name="contactNumber"><br>

        <label>Admin Username <em>*</em></label>
        <input type="text" class="OHRMAdminUserName" id="OHRMAdminUserName" name="OHRMAdminUserName" value="Admin"><br>

        <label>Admin User Password <em>*</em></label>
        <input type="password" class="OHRMAdminPassword" id="OHRMAdminPassword" name="OHRMAdminPassword" value=""><br>

        <label>Confirm Admin User Password <em>*</em></label>
        <input type="password" class="OHRMAdminPasswordConfirm" id="OHRMAdminPasswordConfirm" name="OHRMAdminPasswordConfirm" value=""><br>

        <p class="credentialsNotice">This will be the user credentials to login OrangeHRM as an administrator.</p>

        <p class="requiredFields"><span class="required"> * </span>Required Fields</p>

        <p class="userDirect">Users who seek access to their data, or who seek to correct, amend, or delete the given information should direct their requests to​ Data@orangehrm.com</p>

        <input class="button" type="button" value="Back" onclick="back();"/>
        <input class="button" type="button" value="Next" id="systemConfigReg"/>
    </form>

</div>

<script language="JavaScript">
    $(document).ready(function () {
        $("#registrationForm").validate({
            rules: {
                'organizationName': {
                    required: true,
                },
                'adminEmployeeFirstName': {
                    fistNameRequired: true,
                },
                'adminEmployeeLastName': {
                    lastNameRequired: true,
                },
                'organizationEmailAddress': {
                    required: true,
                    email: true
                },
                'contactNumber': {
                    phone: true
                },
                'OHRMAdminUserName': {
                    required: true,
                    adminLength: true
                },
                'OHRMAdminPassword': {
                    required: true
                },
                'OHRMAdminPasswordConfirm': {
                    required: true,
                    passwordMatch: true
                }

            },
            messages: {
                'organizationName': {
                    required: "Required",
                },
                'adminEmployeeFirstName': {
                    fistNameRequired: "",
                },
                'adminEmployeeLastName': {
                    lastNameRequired: "First Name and last name Required",
                },
                'organizationEmailAddress': {
                    required: "Required",
                    email: "Expected format: admin@example.com"
                },
                'contactNumber': {
                    phone: "Allows numbers and only + - / ( )"
                },
                'OHRMAdminUserName': {
                    required: "Required",
                    adminLength: "OrangeHRM Admin User-name should be at least 5 char. long!"
                },
                'OHRMAdminPassword': {
                    required: "Required"
                },
                'OHRMAdminPasswordConfirm': {
                    required: "Required",
                    passwordMatch: "Admin Password and Confirm Admin Password do not match!"
                }
            }
        });

        $("#systemConfigReg").click(function(){
            if ($("#registrationForm").valid()) {
                $("#actionResponse").val('DEFUSERINFO');
                $("#registrationForm").submit();
            }

        });

        $.validator.addMethod("phone", function(value, element) {
            return (checkPhone(element));
        });

        $.validator.addMethod("adminLength", function(value, element) {
            return (validateAdminUserNameLength());
        });

        $.validator.addMethod("validEmail", function(value, element) {
            return (validateEmail(element));
        });

        $.validator.addMethod("passwordMatch", function(value, element) {
            return (validatePasswordMatch());
        });

        $.validator.addMethod("fistNameLastNameRequired", function(value, element) {
            return (validateFirstNameLastName());
        });

        $.validator.addMethod("fistNameRequired", function(value, element) {
            return (validateFirstName());
        });

        $.validator.addMethod("lastNameRequired", function(value, element) {
            return (validateLastName());
        });

        //check to see whether a valid phone number
        // Space character, plus sign and dash are allowed in phone numbers
        function checkPhone(txt)
        {
            var flag=true;

            for(i=0;txt.value.length>i;i++){

                code=txt.value.charCodeAt(i);

                if ( ( (code>=48) && (code<=57) ) || (code == 45) || (code == 47) || (code == 40) || (code == 41) || (code == 43) || (code == 32) ) {
                    flag=true;
                }
                else
                {
                    flag=false;
                    break;
                }
            }
            return flag;
        }

        function validateEmail(email) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test(email);
        }

        function validatePasswordMatch() {
            var password = $('#OHRMAdminPassword').val();
            var confirmPassword = $('#OHRMAdminPasswordConfirm').val();

            if(password) {
                if (password == confirmPassword){
                    return true;
                } else {
                    return false;
                }
            }
            return true;
        }

        function validateAdminUserNameLength() {
            var adminUserName = $('#OHRMAdminUserName').val();

            if(adminUserName.length < 5) {
                return false;
            }
            return true;
        }

        function validateFirstNameLastName() {
            var firstName = $('#adminEmployeeFirstName').val();
            var lastName = $('#adminEmployeeLastName').val();

            if(firstName == '' || lastName == '') {
                return false;
            }
            return true;
        }

        function validateFirstName() {
            var firstName = $('#adminEmployeeFirstName').val();
            if(firstName == '') {
                nameErrorSet = true;
                return false;
            }
            return true;
        }

        function validateLastName() {
            var lastName = $('#adminEmployeeLastName').val();
            if(lastName == '') {
                nameErrorSet = true;
                return false;
            }
            return true;
        }
    });

</script>