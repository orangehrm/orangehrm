<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 14/9/18
 * Time: 2:44 PM
 */
?>
    <div class="head">
        <h1><?php echo __('Selected Employee'); ?></h1>
    </div>
    <div class="inner">
        <div class="container">
            <div class="col s12 m3 l3 empImage">
                <img class="circle" style="width:128px; height:128px;" src="<?php echo url_for("pim/viewPhoto?empNumber=". $empNumber); ?>"/>
            </div>

            <div class="input-field col s12 m12 l6 empImage">
                <input id="first_name" type="text" disabled="disabled" value="<?php echo $firstName; ?>">
                <lable><span>First name</span></lable>
            </div>

            <div class="input-field col s12 m12 l6 empImage">
                <input id="first_name" type="text" disabled="disabled" value="<?php echo $middleName; ?>">
                <lable><span>Middle name</span></lable>
            </div>

            <div class="input-field col s12 m12 l6 empImage">
                <input id="first_name" type="text" disabled="disabled" value="<?php echo $lastName; ?>">
                <lable><span>Last name</span></lable>
            </div>

            <div class="input-field col s12 m12 l6 empImage">
                <input id="first_name" type="text" disabled="disabled" value="<?php echo $employeeId; ?>">
                <lable><span>Employee Id</span></lable>
            </div>
        </div>
    </div>
<div class="input-field col s12 m12 l12 " id="purgeButton">
    <input type="submit" value="Purge">
</div>

<style>
    .empImage {
        float: left;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;zing: border-box;
        padding: 0 0.75rem;
        padding-top: 0.75rem;
        display: block;
    }

</style>
