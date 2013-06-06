<?php
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
// all the essential functionalities required for any enterprise.
// Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

// OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
// the GNU General Public License as published by the Free Software Foundation; either
// version 2 of the License, or (at your option) any later version.

// OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
// without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU General Public License for more details.

// You should have received a copy of the GNU General Public License along with this program;
// if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
// Boston, MA  02110-1301, USA
*/
?>

<?php

$messageTypes = array('success', 'warning', 'error');
$fadableMessages = false;

foreach ($messageTypes as $messageType) :

    $flashName = $messageType;

    if (isset($prefix)) {
        $flashName = $prefix . '.' . $messageType;
    }    
    
    $cssClass = $messageType;
    $message = null;
    if ($sf_user->hasFlash($flashName)) {
        $message = $sf_user->getFlash($flashName);
        $cssClass .= ' fadable';
        $fadableMessages = true;
    } else if ($sf_user->hasFlash($flashName . '.nofade')) {
        $message = $sf_user->getFlash($flashName. '.nofade');
    }
    if (!is_null($message)) : 
?>
<div class="message <?php echo $cssClass;?>">
<?php

    if (is_array($message) || $message instanceof sfOutputEscaperArrayDecorator) :
        echo "<ol>";
        foreach ($message as $m):
            echo "<li>" . $m . "</li>";
        endforeach;
        echo "</ol>";
    else:
        echo $message;
    endif;
?>   
    <a href="#" class="messageCloseButton"><?php echo __('Close');?></a>
</div>
<?php
    endif; 
endforeach;

if ($fadableMessages) :
?>
<script type="text/javascript">
//<![CDATA[
    $("div.fadable").delay(2000)
        .fadeOut("slow", function () {
            $("div.fadable").remove();
        }); 
//<![CDATA[
</script>
<?php
endif;
?>


