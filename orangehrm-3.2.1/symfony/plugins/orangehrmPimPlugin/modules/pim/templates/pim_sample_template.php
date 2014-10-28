<!-- common table structure to be followed -->
<table cellspacing="0" cellpadding="0" border="1" width="100%">
    <tr>
        <td width="5">&nbsp;</td>
        <td colspan="2" height="30"><?php if($showBackButton) {?><input type="button" class="backbutton" value="<?php echo __("Back") ?>" onclick="goBack();" /><?php }?></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <!-- this space is reserved for menus - dont use -->
        <td width="200" valign="top"><?php require_once 'leftMenu.php';?></td>
        <td valign="top">Have the content</td>
    </tr>
</table>