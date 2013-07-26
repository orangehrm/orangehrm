<div id="sidebar">

    <?php
    $width = '200';
    $height = '200';

    if ((!empty($empPicture)) && ($photographPermissions->canRead())) {
        $width = $empPicture->width;
        $height = $empPicture->height;
    }

    include_partial('photo', array('empNumber' => $empNumber,
        'width' => $width, 'height' => $height,
        'editMode' => isset($editPhotoMode) ? $editPhotoMode : false,
        'fullName' => htmlspecialchars($form->fullName), 'photographPermissions' => $photographPermissions));
    ?>        

    <ul id="sidenav">
        <?php
        foreach ($menuItems as $action => $properties):
            $label = $properties['label'];
            $listClass = ($action == $currentAction) ? ' class="selected"' : '';
            $url = url_for($properties['module'] . '/' . $action . '?empNumber=' . $empNumber);
            ?>
            <li<?php echo $listClass; ?>><a href="<?php echo $url; ?>"><?php echo __($label); ?></a></li>
            <?php
        endforeach;
        ?>
        <?php include_component('core', 'ohrmPluginPannel', array('location' => 'pim_left_menu_bottom')); ?>
    </ul>

</div> <!-- sidebar -->
