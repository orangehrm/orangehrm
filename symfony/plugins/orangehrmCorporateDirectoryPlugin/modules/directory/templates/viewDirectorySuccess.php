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

<?php
use_stylesheets_for_form($form);
use_javascript(plugin_web_path('orangehrmCorporateDirectoryPlugin', 'js/viewDirectorySuccess'));
?>
<div class="box searchForm toggableForm">
    <div class="head">       
        <h1><?php echo __("Search Directory") ?></h1>
    </div>
    <div class="inner">
        <form name="frmDirectorySearch" id="search_form" method="post" action="<?php echo url_for('directory/viewDirectory'); ?>">
            <fieldset>

                <ol>
<?php echo $form->render(); ?>

                </ol>
                <input type="hidden" name="pageNo" id="pageNo" value="" />
                <input type="hidden" name="hdnAction" id="hdnAction" value="search" />      
                <p>
                    <input type="submit" id="searchBtn" value="<?php echo __("Search") ?>" name="_search" />
                    <input type="button" class="reset" id="resetBtn" value="<?php echo __("Reset") ?>" name="_reset" />                    
                </p>
            </fieldset>     
        </form>
    </div>
    <a href="#" class="toggle tiptip" title="<?php echo __(CommonMessages::TOGGABLE_DEFAULT_MESSAGE); ?>">&gt;</a>
</div>
<?php
if ((sizeof($list)) > 0) {
    ?>
    <div id="searchResults" class="box">
        <div id="resultBox" class="inner">
            <div id="divResults" ><h1><? //php echo __("Search Results")  ?></h1>            
                    <?php if ($isPaginated) { ?>
                    <ul class="paging top">
                    <?php include_partial('global/paging_links_js', array('pager' => $pager)); ?>
                    </ul>
    <?php }; ?>
            </div>        
            <table id="resultTable" class="table hover">           
                <tr>
                    <?php
                    $i = 1;
                    foreach ($list as $emp) {
                        $class = ($i % 2 ) == 1 ? 'odd' : 'even';
                        $i++
                        ?>
                    <tr class="<?php echo $class; ?>">
                        <td>                        
                            <img alt="Employee Photo" src="<?php echo url_for("directory/viewDirectoryPhoto?empNumber=" . $emp->getEmpNumber()); ?>" border="0" id="empPic" width="60"/>
                        </td>
                        <td style='width: 80%;'>
                            <ul style='font-size: 20px;width: 50%;'>
                                <li><b><?php echo $emp->getFullName(); ?></b></li>
                                <li style='font-size: 12px;'><?php echo $emp->getJobTitleName(); ?></li>
                                <li style='font-size: 12px;'><?php echo $emp->getSubDivision(); ?></li>
        <?php $locs = $emp->getLocations(); ?>
                                <li style='font-size: 12px;'><?php echo $locs[0]; ?></li>
                                <li style='font-size: 12px;'>
                                    <?php
                                    echo $emp->getEmpWorkTelephone();
                                    if (($emp->getEmpWorkTelephone() != NULL && $emp->getEmpWorkEmail() != NULL)) {
                                        echo ', ' . $emp->getEmpWorkEmail();
                                    } else {
                                        echo $emp->getEmpWorkEmail();
                                    };
                                    ?>
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tr>
                    
            </table>
            <?php if ($isPaginated) { ?>
                <div class="navigationHearder" >
                    <div class="pagingbar">
        <?php include_partial('global/paging_links_js', array('pager' => $pager)); ?>
                    </div>
                </div>
            <?php
            } ?>
        </div>      
<?php
        } else {
            ?>
            <div  class="box searchForm">
                <div id="divNoResults" class="head">
                    <h1></h1>
                </div>
                <div class="inner"> 
                        <table id="resultTable" class="table hover">           
                            <tr><?php echo __("No Records Found"); ?></tr>
                        </table>
                </div>      
            </div>        
<?php } ?>    
    </div>
</div>      