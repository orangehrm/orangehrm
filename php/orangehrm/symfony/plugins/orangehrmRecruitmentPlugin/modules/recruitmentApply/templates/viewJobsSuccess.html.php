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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php 
$cultureElements = explode('_', $sf_user->getCulture());
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $cultureElements[0]; ?>" lang="<?php echo $cultureElements[0]; ?>">

    <head>
        
        <title><?php echo __('Active Job Vacancies'); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
        <?php use_stylesheet('../orangehrmRecruitmentPlugin/css/viewJobsSuccess'); ?>
        <?php use_javascript('../../../scripts/jquery/jquery.js'); ?>
        <?php use_javascript('../orangehrmRecruitmentPlugin/js/viewJobsSuccess'); ?>

    </head>

    <body>

        <div id="jobPage">

            <div class="outerbox">

                <div class="maincontent">

                    <div class="mainHeading">
                        <h2><?php echo __('Active Job Vacancies'); ?></h2>
                    </div>

                    <?php if(count($publishedVacancies) != 0): ?>
                    
                        <div id="toggleJobList">
                            <span id="expandJobList"><?php echo __('Expand all') ?></span> | <span id="collapsJobList"><?php echo __('Collapse all'); ?></span>
                        </div>

                        <?php foreach ($publishedVacancies as $vacancy): ?>

                            <div class="plusOrMinusmark">
                                <span class="plusMark">[+]</span><span class="minusMark">[-]</span>
                            </div>

                            <div class="jobItem">
                                
                                <div class="vacancyTitle">
                                    <h3><?php echo $vacancy->getName(); ?></h3>
                                </div>
                                
                                <pre class="vacancyShortDescription"><?php echo getShortDescription($vacancy->getDescription(), 250, "..."); ?></pre>
                                <pre class="vacancyDescription"><?php echo $vacancy->getDescription(); ?></pre>
                                
                                <input type="button" class="apply" name="applyButton" value="<?php echo __("Apply"); ?>" onmouseout="moutButton(this);" onmouseover="moverButton(this);" />
                                <a href="<?php echo public_path('index.php/recruitmentApply/applyVacancy/id/'.$vacancy->getId(), true); ?>" class="applyLink"></a>
                                
                            </div>
                            <hr class="verticalLine" />

                        <?php endforeach; ?>
                    
                    <?php else: ?>
                    
                        <span class="noVacanciesMessage"><?php echo __('No active job vacancies to display'); ?></span>
                    
                    <?php endif; ?>

                </div>

            </div>

        </div>

    </body>

    <script type="text/javascript">
        //<![CDATA[
        if (document.getElementById && document.createElement) {
            roundBorder('outerbox');
        }
        //]]>
    </script>

</html>


<?php

     /*
      * Get short description to show in default view in view job list
      * @param string $description full description
      * @param int $limit Number of characters show in short description
      * @param string $endString String added to end of the short description
      * @return string $description short description 
      */
    function getShortDescription($description, $limit, $endString) {
        
        if(strlen($description) > $limit) {            
            $subString = substr($description, 0, $limit);
            $wordArray = explode(" ", $subString);
            $description = substr($subString, 0, -(strlen($wordArray[count($wordArray)-1])+1)) . $endString;            
        }        
        return $description;
        
    }

?>